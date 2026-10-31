<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MarketingAreaAssignment;
use App\Models\Country;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;

class EmployeeMarketingAreaTreeController extends Controller
{
    public function index()
    {
        // Use the same employee lookup as Marketing Chain.
        $employee = $this->getLoggedInEmployee();

        if (!$employee) {
            abort(403, 'Employee account not found.');
        }

        // IMPORTANT: Use Employee model ID, not auth()->id().
        $assignments = MarketingAreaAssignment::with('scopes')
            ->where('member_type', USER_TYPE_EMPLOYEE)
            ->where('member_id', $employee->id)
            ->get();

        $tree = [];

        foreach ($assignments as $assignment) {
            foreach ($assignment->scopes as $scope) {
                switch ($scope->scope_type) {
                    case 'country':
                        $country = Country::find($scope->scope_id);

                        if ($country) {
                            $this->addCountry($tree, $country);
                        }
                        break;

                    case 'division':
                        $division = Division::find($scope->scope_id);

                        if ($division) {
                            $country = Country::find($division->country_id);

                            if ($country) {
                                $this->addCountry($tree, $country);
                                $this->addDivision(
                                    $tree,
                                    $country,
                                    $division
                                );
                            }
                        }
                        break;

                    case 'district':
                        $district = District::find($scope->scope_id);

                        if ($district) {
                            $division = Division::find(
                                $district->division_id
                            );

                            $country = $division
                                ? Country::find($division->country_id)
                                : null;

                            if ($country && $division) {
                                $this->addCountry($tree, $country);
                                $this->addDivision(
                                    $tree,
                                    $country,
                                    $division
                                );
                                $this->addDistrict(
                                    $tree,
                                    $country,
                                    $division,
                                    $district
                                );
                            }
                        }
                        break;

                    case 'upazila':
                        $upazila = Upazila::find($scope->scope_id);

                        if ($upazila) {
                            $district = District::find(
                                $upazila->district_id
                            );

                            $division = $district
                                ? Division::find($district->division_id)
                                : null;

                            $country = $division
                                ? Country::find($division->country_id)
                                : null;

                            if ($country && $division && $district) {
                                $this->addCountry($tree, $country);
                                $this->addDivision(
                                    $tree,
                                    $country,
                                    $division
                                );
                                $this->addDistrict(
                                    $tree,
                                    $country,
                                    $division,
                                    $district
                                );
                                $this->addUpazila(
                                    $tree,
                                    $country,
                                    $division,
                                    $district,
                                    $upazila
                                );
                            }
                        }
                        break;
                }
            }
        }

        $tree = $this->formatTree($tree);

        return view(
            'employee.marketing.area-tree',
            compact('employee', 'tree')
        );
    }

    private function getLoggedInEmployee()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user instanceof Employee) {
            return $user;
        }

        if (method_exists($user, 'employee') && $user->employee) {
            return $user->employee;
        }

        if (!empty($user->employee_id)) {
            return Employee::find($user->employee_id);
        }

        return null;
    }

    private function areaName($model)
    {
        return $model->name_bn
            ?: ($model->name_en ?? $model->name ?? 'Unknown');
    }

    private function addCountry(&$tree, $country)
    {
        $id = $country->id;

        if (!isset($tree[$id])) {
            $tree[$id] = [
                'id' => $id,
                'name' => $this->areaName($country),
                'type' => 'country',
                'children' => [],
            ];
        }
    }

    private function addDivision(&$tree, $country, $division)
    {
        $this->addCountry($tree, $country);

        $countryId = $country->id;
        $divisionId = $division->id;

        if (!isset($tree[$countryId]['children'][$divisionId])) {
            $tree[$countryId]['children'][$divisionId] = [
                'id' => $divisionId,
                'name' => $this->areaName($division),
                'type' => 'division',
                'children' => [],
            ];
        }
    }

    private function addDistrict(
        &$tree,
        $country,
        $division,
        $district
    ) {
        $this->addDivision($tree, $country, $division);

        $countryId = $country->id;
        $divisionId = $division->id;
        $districtId = $district->id;

        if (!isset(
            $tree[$countryId]['children'][$divisionId]
                ['children'][$districtId]
        )) {
            $tree[$countryId]['children'][$divisionId]
                ['children'][$districtId] = [
                    'id' => $districtId,
                    'name' => $this->areaName($district),
                    'type' => 'district',
                    'children' => [],
                ];
        }
    }

    private function addUpazila(
        &$tree,
        $country,
        $division,
        $district,
        $upazila
    ) {
        $this->addDistrict(
            $tree,
            $country,
            $division,
            $district
        );

        $countryId = $country->id;
        $divisionId = $division->id;
        $districtId = $district->id;
        $upazilaId = $upazila->id;

        $tree[$countryId]['children'][$divisionId]
            ['children'][$districtId]['children'][$upazilaId] ??= [
                'id' => $upazilaId,
                'name' => $this->areaName($upazila),
                'type' => 'upazila',
            ];
    }

    private function formatTree($nodes)
    {
        $result = [];

        foreach ($nodes as $node) {
            if (isset($node['children'])) {
                $node['children'] = $this->formatTree(
                    $node['children']
                );
            }

            $result[] = $node;
        }

        return $result;
    }
}
