
@extends('layouts.employee')

@section('content')

<div class="dashboard-title mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2>Marketing Area Tree</h2>
        <p>View your assigned marketing areas and locations</p>
    </div>

    <div>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home mr-1"></i>
            Dashboard
        </a>
    </div>
</div>

<section class="section">

    <div class="card">

        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-sitemap mr-2"></i>
                    My Assigned Areas
                </h4>

                <small class="text-muted">
                    Country, Division, District and Upazila hierarchy
                </small>
            </div>

            <span class="badge badge-primary">
                <i class="fas fa-eye mr-1"></i>
                View Only
            </span>
        </div>

        <div class="card-body">

            @if(!empty($tree))

                <div class="marketing-area-tree">

                    @php
                        $renderAreaNode = function ($node, $level = 0)
                            use (&$renderAreaNode) {

                            $type = $node['type'] ?? 'area';

                            $icons = [
                                'country'  => 'fa-globe',
                                'division' => 'fa-map',
                                'district' => 'fa-map-marked-alt',
                                'upazila'  => 'fa-map-marker-alt',
                            ];

                            $colors = [
                                'country'  => 'area-country',
                                'division' => 'area-division',
                                'district' => 'area-district',
                                'upazila'  => 'area-upazila',
                            ];

                            $labels = [
                                'country'  => 'Country',
                                'division' => 'Division',
                                'district' => 'District',
                                'upazila'  => 'Upazila',
                            ];

                            $icon = $icons[$type] ?? 'fa-map-marker-alt';
                            $color = $colors[$type] ?? 'area-upazila';
                            $label = $labels[$type] ?? 'Area';

                            $children = $node['children'] ?? [];
                            $hasChildren = !empty($children);

                            $nodeId = 'area-' . $type . '-' . ($node['id'] ?? uniqid());

                            echo '<div class="area-tree-node">';

                            echo '<div class="area-tree-item ' . $color . '"
                                      style="--tree-level:' . (int) $level . ';">';

                            if ($hasChildren) {
                                echo '<button type="button"
                                            class="area-toggle"
                                            aria-expanded="true"
                                            aria-controls="' . e($nodeId) . '"
                                            title="Expand or collapse">
                                        <i class="fas fa-chevron-down"></i>
                                      </button>';
                            } else {
                                echo '<span class="area-toggle-placeholder">
                                        <i class="fas fa-circle"></i>
                                      </span>';
                            }

                            echo '<div class="area-tree-icon">
                                    <i class="fas ' . $icon . '"></i>
                                  </div>';

                            echo '<div class="area-tree-info">
                                    <div class="area-tree-name">' .
                                        e($node['name'] ?? '-') .
                                    '</div>
                                    <small class="text-muted">' .
                                        e($label) .
                                    '</small>
                                  </div>';

                            if ($hasChildren) {
                                echo '<span class="badge badge-light area-count">' .
                                        count($children) .
                                        ' ' .
                                        (count($children) === 1 ? 'child' : 'children') .
                                     '</span>';
                            }

                            echo '</div>';

                            if ($hasChildren) {
                                echo '<div class="area-tree-children"
                                           id="' . e($nodeId) . '">';

                                foreach ($children as $child) {
                                    $renderAreaNode($child, $level + 1);
                                }

                                echo '</div>';
                            }

                            echo '</div>';
                        };
                    @endphp

                    @foreach($tree as $root)
                        @php
                            $renderAreaNode($root);
                        @endphp
                    @endforeach

                </div>

            @else

                <div class="text-center py-5">

                    <div class="area-empty-icon mb-3">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>

                    <h5>No Marketing Areas Assigned</h5>

                    <p class="text-muted mb-0">
                        No marketing areas have been assigned to your account yet.
                        Please contact your administrator.
                    </p>

                </div>

            @endif

        </div>
    </div>

</section>

<style>
    .marketing-area-tree {
        width: 100%;
        padding: 5px 0;
    }

    .area-tree-node {
        position: relative;
    }

    .area-tree-item {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 66px;
        margin-bottom: 10px;
        padding: 10px 15px;
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        background: #fff;
        transition: box-shadow .2s ease, border-color .2s ease;
    }

    .area-tree-item:hover {
        border-color: #c7cfff;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
    }

    .area-toggle,
    .area-toggle-placeholder {
        width: 25px;
        min-width: 25px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: transparent;
        color: #6777ef;
    }

    .area-toggle {
        cursor: pointer;
        border-radius: 4px;
    }

    .area-toggle:hover {
        background: #f0f3ff;
    }

    .area-toggle-placeholder {
        font-size: 7px;
        color: #adb5bd;
    }

    .area-tree-icon {
        width: 43px;
        min-width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff;
        font-size: 17px;
    }

    .area-country .area-tree-icon {
        background: #6777ef;
    }

    .area-division .area-tree-icon {
        background: #54ca68;
    }

    .area-district .area-tree-icon {
        background: #ffa426;
    }

    .area-upazila .area-tree-icon {
        background: #3abaf4;
    }

    .area-tree-info {
        flex: 1;
        min-width: 0;
    }

    .area-tree-name {
        font-size: 15px;
        font-weight: 600;
        color: #34395e;
        overflow-wrap: anywhere;
    }

    .area-count {
        white-space: nowrap;
        font-size: 11px;
        color: #6777ef;
    }

    .area-tree-children {
        margin-left: 29px;
        padding-left: 20px;
        border-left: 2px solid #e5e8f5;
    }

    .area-empty-icon {
        width: 70px;
        height: 70px;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f0f3ff;
        color: #6777ef;
        font-size: 30px;
    }

    @media (max-width: 576px) {
        .area-tree-item {
            gap: 8px;
            padding: 10px 8px;
        }

        .area-tree-icon {
            width: 36px;
            min-width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .area-tree-children {
            margin-left: 12px;
            padding-left: 10px;
        }

        .area-count {
            font-size: 10px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.area-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('aria-controls');
                const children = document.getElementById(targetId);

                if (!children) {
                    return;
                }

                const isExpanded =
                    button.getAttribute('aria-expanded') === 'true';

                button.setAttribute('aria-expanded', String(!isExpanded));
                children.style.display = isExpanded ? 'none' : 'block';

                const icon = button.querySelector('i');

                if (icon) {
                    icon.classList.toggle('fa-chevron-down', !isExpanded);
                    icon.classList.toggle('fa-chevron-right', isExpanded);
                }
            });
        });
    });
</script>

@endsection
