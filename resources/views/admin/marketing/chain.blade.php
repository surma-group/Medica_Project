@extends('layouts.admin')

@section('title', 'Marketing Chain')

@section('content')

<div class="section">

    <div class="section-header">

        <h1>Marketing Chain</h1>

        <div class="section-header-breadcrumb">

            <div class="breadcrumb-item">
                Marketing
            </div>

            <div class="breadcrumb-item active">
                Marketing Chain
            </div>

        </div>

    </div>

    <div class="section-body">

        <div id="relationMessage"></div>

        <div class="card shadow-sm mb-3">

            <div class="card-header">
                <h4>Marketing Hierarchy & Relations</h4>
            </div>

            <div class="card-body">

                <div class="alert alert-info">

                    <strong>Hierarchy:</strong>

                    Chairman → Managing Director → Deputy Managing Director
                    → General Manager → Assistant General Manager
                    → Area Manager → Assistant Manager → Manager
                    → Senior Sales Executive Manager → Sales Executive
                    → Delivery Man / Dealer → Agent

                </div>

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>Parent Type</label>

                            <select
                                id="parentType"
                                class="form-control">

                                <option value="">
                                    Select Parent Type
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>Parent</label>

                            <select
                                id="parentId"
                                class="form-control">

                                <option value="">
                                    Select Parent
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>Parent Designation</label>

                            <input
                                type="text"
                                id="parentDesignation"
                                class="form-control"
                                readonly>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>Child Type</label>

                            <select
                                id="childType"
                                class="form-control">

                                <option value="">
                                    Select Child Type
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-5">

                        <div class="form-group">

                            <label>Child</label>

                            <select
                                id="childId"
                                class="form-control">

                                <option value="">
                                    Select Child
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-group">

                            <label>&nbsp;</label>

                            <button
                                type="button"
                                id="addRelation"
                                class="btn btn-primary btn-block">

                                <i class="fas fa-plus"></i>
                                Add Relation

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="card shadow-sm mb-3">

            <div class="card-header">

                <h4>Current Marketing Relations</h4>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-striped table-hover mb-0">

                        <thead>

                            <tr>

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Parent
                                </th>

                                <th>
                                    Parent Type
                                </th>

                                <th>
                                    Child
                                </th>

                                <th>
                                    Child Type
                                </th>

                                <th width="120">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody id="relationsTableBody"></tbody>

                    </table>

                </div>

            </div>

        </div>


        <div class="card shadow-sm mb-3">

            <div class="card-header">

                <h4>Full Marketing Chain</h4>

            </div>

            <div class="card-body">

                <div id="marketingChain"></div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
    /*
|--------------------------------------------------------------------------
| USER TYPE
|--------------------------------------------------------------------------
*/

    const USER_TYPE_DEALER = 2;
    const USER_TYPE_AGENT = 3;
    const USER_TYPE_EMPLOYEE = 5;


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    const employeeData =
        @json($employeeData ?? []);

    const dealerData =
        @json($dealerData ?? []);

    const agentData =
        @json($agentData ?? []);

    const designationData =
        @json($designationData ?? []);

    let relationsData =
        @json($relationData ?? []);

    const chainData =
        @json($chain ?? []);


    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    */

    const storeUrl =
        "{{ route('marketing.chain.relation.store') }}";

    const cancelUrl =
        "{{ url('/em_admin/marketing/chain/relation') }}";

    const csrfToken =
        "{{ csrf_token() }}";


    /*
    |--------------------------------------------------------------------------
    | DATA MAP
    |--------------------------------------------------------------------------
    */

    const employees = {};
    const dealers = {};
    const agents = {};
    const designations = {};


    employeeData.forEach(function(employee) {

        employees[String(employee.id)] = employee;

    });


    dealerData.forEach(function(dealer) {

        dealers[String(dealer.id)] = dealer;

    });


    agentData.forEach(function(agent) {

        agents[String(agent.id)] = agent;

    });


    designationData.forEach(function(designation) {

        designations[String(designation.id)] =
            designation;

    });


    /*
    |--------------------------------------------------------------------------
    | ENTITY
    |--------------------------------------------------------------------------
    */

    function getEmployee(id) {
        return employees[String(id)] || null;
    }


    function getDealer(id) {
        return dealers[String(id)] || null;
    }


    function getAgent(id) {
        return agents[String(id)] || null;
    }


    function getEntity(type, id) {
        type = Number(type);

        id = String(id);


        if (type === USER_TYPE_EMPLOYEE) {

            return getEmployee(id);

        }


        if (type === USER_TYPE_DEALER) {

            return getDealer(id);

        }


        if (type === USER_TYPE_AGENT) {

            return getAgent(id);

        }


        return null;
    }


    function getEntityName(type, id) {
        const entity =
            getEntity(type, id);


        if (!entity) {

            return 'Unknown';

        }


        type = Number(type);


        if (type === USER_TYPE_EMPLOYEE) {

            return entity.full_name ||
                entity.name ||
                'Employee';

        }


        return entity.name || 'Unknown';
    }


    function getEntityTypeName(type) {
        switch (Number(type)) {

            case USER_TYPE_EMPLOYEE:
                return 'Employee';

            case USER_TYPE_DEALER:
                return 'Dealer';

            case USER_TYPE_AGENT:
                return 'Agent';

            default:
                return 'Unknown';

        }
    }


    function getEntityDesignation(type, id) {
        type = Number(type);


        const entity =
            getEntity(type, id);


        if (!entity) {

            return '';

        }


        if (type === USER_TYPE_EMPLOYEE) {

            if (entity.designation) {

                if (
                    typeof entity.designation ===
                    'string'
                ) {

                    return entity.designation;

                }


                return entity.designation.name || '';

            }


            if (
                entity.designation_id &&
                designations[
                    String(entity.designation_id)
                ]
            ) {

                return designations[
                    String(entity.designation_id)
                ].name || '';

            }

        }


        if (type === USER_TYPE_DEALER) {

            return 'Dealer';

        }


        if (type === USER_TYPE_AGENT) {

            return 'Agent';

        }


        return '';
    }


    function getEntityCode(type, id) {
        type = Number(type);


        if (type !== USER_TYPE_EMPLOYEE) {

            return '';

        }


        const employee =
            getEmployee(id);


        if (!employee) {

            return '';

        }


        return employee.employee_code || '';
    }


    function getEntityIcon(type) {
        type = Number(type);


        if (type === USER_TYPE_EMPLOYEE) {

            return 'fa-user-tie';

        }


        if (type === USER_TYPE_DEALER) {

            return 'fa-store';

        }


        if (type === USER_TYPE_AGENT) {

            return 'fa-user';

        }


        return 'fa-user';
    }


    function entityKey(type, id) {
        return Number(type) +
            ':' +
            String(id);
    }


    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    function showMessage(type, message) {
        $('#relationMessage').html(

            '<div class="alert alert-' +
            type +
            ' alert-dismissible fade show">' +

            message +

            '<button type="button" class="close" data-dismiss="alert">' +

            '<span>&times;</span>' +

            '</button>' +

            '</div>'

        );


        setTimeout(function() {

            $('#relationMessage .alert')
                .alert('close');

        }, 4000);
    }


    /*
    |--------------------------------------------------------------------------
    | PARENT TYPES
    |--------------------------------------------------------------------------
    */

    function populateParentTypes() {
        $('#parentType').html(`

        <option value="">
            Select Parent Type
        </option>

        <option value="${USER_TYPE_EMPLOYEE}">
            Employee
        </option>

        <option value="${USER_TYPE_DEALER}">
            Dealer
        </option>

    `);
    }


    /*
    |--------------------------------------------------------------------------
    | PARENTS
    |--------------------------------------------------------------------------
    */

    function populateParents() {
        const type =
            Number(
                $('#parentType').val()
            );


        $('#parentId').html(
            '<option value="">Select Parent</option>'
        );


        $('#parentDesignation').val('');


        if (!type) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Employee
        |--------------------------------------------------------------------------
        */

        if (
            type === USER_TYPE_EMPLOYEE
        ) {

            employeeData.forEach(function(employee) {

                const designation =
                    getEntityDesignation(
                        USER_TYPE_EMPLOYEE,
                        employee.id
                    );


                $('#parentId').append(

                    $('<option>', {

                        value: employee.id,

                        text: (
                                employee.full_name ||
                                employee.name ||
                                'Employee'
                            ) +

                            (
                                designation ?
                                ' — ' +
                                designation :
                                ''
                            )

                    })

                );

            });


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Dealer
        |--------------------------------------------------------------------------
        */

        if (
            type === USER_TYPE_DEALER
        ) {

            dealerData.forEach(function(dealer) {

                $('#parentId').append(

                    $('<option>', {

                        value: dealer.id,

                        text: (
                                dealer.name ||
                                'Dealer'
                            ) +

                            (
                                dealer.shop_name ?
                                ' — ' +
                                dealer.shop_name :
                                ''
                            )

                    })

                );

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CHILD TYPES
    |--------------------------------------------------------------------------
    */

    function populateChildTypes() {
        const parentType =
            Number(
                $('#parentType').val()
            );


        const parentId =
            $('#parentId').val();


        $('#childType').html(
            '<option value="">Select Child Type</option>'
        );


        $('#childId').html(
            '<option value="">Select Child</option>'
        );


        if (
            !parentType ||
            !parentId
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Employee Parent
        |--------------------------------------------------------------------------
        */

        if (
            parentType === USER_TYPE_EMPLOYEE
        ) {

            const parent =
                getEmployee(parentId);


            if (!parent) {

                return;

            }


            const designation =
                getEntityDesignation(
                    USER_TYPE_EMPLOYEE,
                    parentId
                );


            /*
            |--------------------------------------------------------------------------
            | Sales Executive → Employee + Dealer
            |--------------------------------------------------------------------------
            */

            if (
                designation &&
                designation
                .trim()
                .toUpperCase() ===
                'SALES EXECUTIVE'
            ) {

                $('#childType').append(`

                <option value="${USER_TYPE_EMPLOYEE}">
                    Employee
                </option>

                <option value="${USER_TYPE_DEALER}">
                    Dealer
                </option>

            `);

            }


            /*
            |--------------------------------------------------------------------------
            | Other Employee → Employee
            |--------------------------------------------------------------------------
            */
            else {

                $('#childType').append(`

                <option value="${USER_TYPE_EMPLOYEE}">
                    Employee
                </option>

            `);

            }


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Dealer → Agent
        |--------------------------------------------------------------------------
        */

        if (
            parentType === USER_TYPE_DEALER
        ) {

            $('#childType').append(`

            <option value="${USER_TYPE_AGENT}">
                Agent
            </option>

        `);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CHILDREN
    |--------------------------------------------------------------------------
    */

    function populateChildren() {
        const parentType =
            Number(
                $('#parentType').val()
            );


        const parentId =
            $('#parentId').val();


        const childType =
            Number(
                $('#childType').val()
            );


        $('#childId').html(
            '<option value="">Select Child</option>'
        );


        if (
            !parentType ||
            !parentId ||
            !childType
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Employee → Employee
        |--------------------------------------------------------------------------
        */

        if (
            parentType === USER_TYPE_EMPLOYEE &&
            childType === USER_TYPE_EMPLOYEE
        ) {

            const parent =
                getEmployee(parentId);


            if (
                !parent ||
                !parent.designation_id
            ) {

                return;

            }


            const parentDesignation =
                designations[
                    String(parent.designation_id)
                ];


            if (!parentDesignation) {

                return;

            }


            employeeData.forEach(function(employee) {

                if (
                    String(employee.id) ===
                    String(parentId)
                ) {

                    return;

                }


                const childDesignation =
                    designations[
                        String(employee.designation_id)
                    ];


                if (!childDesignation) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Direct Designation Child
                |--------------------------------------------------------------------------
                */

                if (
                    String(
                        childDesignation.parent_id
                    ) !==
                    String(
                        parentDesignation.id
                    )
                ) {

                    return;

                }


                $('#childId').append(

                    $('<option>', {

                        value: employee.id,

                        text: (
                                employee.full_name ||
                                employee.name ||
                                'Employee'
                            ) +

                            ' — ' +

                            childDesignation.name +

                            (
                                employee.employee_code ?
                                ' (' +
                                employee.employee_code +
                                ')' :
                                ''
                            )

                    })

                );

            });


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Employee → Dealer
        |--------------------------------------------------------------------------
        */

        if (
            parentType === USER_TYPE_EMPLOYEE &&
            childType === USER_TYPE_DEALER
        ) {

            dealerData.forEach(function(dealer) {

                $('#childId').append(

                    $('<option>', {

                        value: dealer.id,

                        text: (
                                dealer.name ||
                                'Dealer'
                            ) +

                            (
                                dealer.shop_name ?
                                ' — ' +
                                dealer.shop_name :
                                ''
                            )

                    })

                );

            });


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Dealer → Agent
        |--------------------------------------------------------------------------
        */

        if (
            parentType === USER_TYPE_DEALER &&
            childType === USER_TYPE_AGENT
        ) {

            agentData.forEach(function(agent) {

                $('#childId').append(

                    $('<option>', {

                        value: agent.id,

                        text: agent.name ||
                            'Agent'

                    })

                );

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RELATION EXISTS
    |--------------------------------------------------------------------------
    */

    function relationExists(
        parentType,
        parentId,
        childType,
        childId
    ) {
        parentType =
            Number(parentType);


        childType =
            Number(childType);


        return relationsData.some(function(relation) {

            if (
                Number(
                    relation.status
                ) !== 1
            ) {

                return false;

            }


            return (

                Number(
                    relation.parent_type
                ) ===
                parentType &&

                String(
                    relation.parent_id
                ) ===
                String(parentId) &&

                Number(
                    relation.child_type
                ) ===
                childType &&

                String(
                    relation.child_id
                ) ===
                String(childId)

            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | RELATION TABLE
    |--------------------------------------------------------------------------
    */

    function renderRelationsTable() {
        const tbody =
            $('#relationsTableBody');


        tbody.empty();


        const activeRelations =
            relationsData.filter(function(relation) {

                return Number(
                    relation.status
                ) === 1;

            });


        if (!activeRelations.length) {

            tbody.html(`

            <tr>

                <td
                    colspan="6"
                    class="text-center py-4"
                >

                    <div class="text-muted">

                        <i class="fas fa-info-circle"></i>

                        No active marketing relations found.

                    </div>

                </td>

            </tr>

        `);

            return;

        }


        activeRelations.forEach(
            function(relation, index) {

                const parentType =
                    Number(
                        relation.parent_type
                    );


                const childType =
                    Number(
                        relation.child_type
                    );


                const parentName =
                    getEntityName(
                        parentType,
                        relation.parent_id
                    );


                const childName =
                    getEntityName(
                        childType,
                        relation.child_id
                    );


                const parentTypeName =
                    getEntityTypeName(
                        parentType
                    );


                const childTypeName =
                    getEntityTypeName(
                        childType
                    );


                const parentDesignation =
                    getEntityDesignation(
                        parentType,
                        relation.parent_id
                    );


                const childDesignation =
                    getEntityDesignation(
                        childType,
                        relation.child_id
                    );


                const parentCode =
                    getEntityCode(
                        parentType,
                        relation.parent_id
                    );


                const childCode =
                    getEntityCode(
                        childType,
                        relation.child_id
                    );


                const parentInfo =
                    parentName +

                    (
                        parentDesignation ?
                        '<br><small class="text-muted">' +

                        parentDesignation +

                        (
                            parentCode ?
                            ' | ' +
                            parentCode :
                            ''
                        ) +

                        '</small>'

                        :
                        ''
                    );


                const childInfo =
                    childName +

                    (
                        childDesignation ?
                        '<br><small class="text-muted">' +

                        childDesignation +

                        (
                            childCode ?
                            ' | ' +
                            childCode :
                            ''
                        ) +

                        '</small>'

                        :
                        ''
                    );


                const parentBadgeClass =
                    parentTypeName === 'Dealer' ?
                    'badge-warning' :
                    parentTypeName === 'Agent' ?
                    'badge-secondary' :
                    'badge-info';


                const childBadgeClass =
                    childTypeName === 'Dealer' ?
                    'badge-warning' :
                    childTypeName === 'Agent' ?
                    'badge-secondary' :
                    'badge-primary';


                tbody.append(`

                <tr>

                    <td>
                        ${index + 1}
                    </td>

                    <td>
                        ${parentInfo}
                    </td>

                    <td>

                        <span class="badge ${parentBadgeClass}">
                            ${parentTypeName}
                        </span>

                    </td>

                    <td>
                        ${childInfo}
                    </td>

                    <td>

                        <span class="badge ${childBadgeClass}">
                            ${childTypeName}
                        </span>

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn btn-danger btn-sm cancel-relation"
                            data-relation-id="${relation.id}"
                        >

                            <i class="fas fa-times"></i>

                        </button>

                    </td>

                </tr>

            `);

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BUILD LIVE CHAIN
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Only an entity that is NOT a child of another relation
    | becomes a root.
    |
    | Example:
    |
    | Chairman → Managing Director
    | Managing Director → Deputy Managing Director
    |
    | Root:
    | Chairman
    |
    | Final:
    | Chairman
    |    ↓
    | Managing Director
    |    ↓
    | Deputy Managing Director
    |
    |--------------------------------------------------------------------------
    */

    function buildLiveChain() {
        const activeRelations =
            relationsData.filter(function(relation) {

                return Number(
                    relation.status
                ) === 1;

            });


        if (!activeRelations.length) {

            return [];

        }


        const parentKeys =
            new Set();


        const childKeys =
            new Set();


        activeRelations.forEach(function(relation) {

            parentKeys.add(

                entityKey(
                    relation.parent_type,
                    relation.parent_id
                )

            );


            childKeys.add(

                entityKey(
                    relation.child_type,
                    relation.child_id
                )

            );

        });


        /*
        |--------------------------------------------------------------------------
        | Find only actual roots
        |--------------------------------------------------------------------------
        */

        const roots = [];


        parentKeys.forEach(function(key) {

            if (
                !childKeys.has(key)
            ) {

                const parts =
                    key.split(':');


                roots.push({

                    type: Number(parts[0]),

                    id: parts[1]

                });

            }

        });


        const trees = [];


        const rootKeys =
            new Set();


        roots.forEach(function(root) {

            const key =
                entityKey(
                    root.type,
                    root.id
                );


            if (
                rootKeys.has(key)
            ) {

                return;

            }


            rootKeys.add(key);


            trees.push(

                buildLiveNode(
                    root.type,
                    root.id,
                    null,
                    0,
                    []
                )

            );

        });


        return trees;
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD LIVE NODE
    |--------------------------------------------------------------------------
    */

    function buildLiveNode(
        type,
        id,
        relation,
        level,
        path
    ) {
        type =
            Number(type);


        const key =
            entityKey(
                type,
                id
            );


        const nextPath =
            path.slice();


        /*
        |--------------------------------------------------------------------------
        | Prevent circular relation loop
        |--------------------------------------------------------------------------
        */

        if (
            nextPath.includes(key)
        ) {

            return {

                type: type,

                id: id,

                relation: relation,

                level: level,

                children: []

            };

        }


        nextPath.push(key);


        const children = [];


        const activeRelations =
            relationsData.filter(function(item) {

                if (
                    Number(
                        item.status
                    ) !== 1
                ) {

                    return false;

                }


                if (
                    Number(
                        item.parent_type
                    ) !==
                    type
                ) {

                    return false;

                }


                if (
                    String(
                        item.parent_id
                    ) !==
                    String(id)
                ) {

                    return false;

                }


                return true;

            });


        activeRelations.forEach(function(item) {

            children.push(

                buildLiveNode(

                    Number(
                        item.child_type
                    ),

                    item.child_id,

                    item,

                    level + 1,

                    nextPath

                )

            );

        });


        return {

            type: type,

            id: id,

            relation: relation,

            level: level,

            children: children

        };

    }


    /*
    |--------------------------------------------------------------------------
    | RENDER CHAIN NODE
    |--------------------------------------------------------------------------
    */

    function renderChainNode(
        node,
        container
    ) {
        const name =
            getEntityName(
                node.type,
                node.id
            );


        const typeName =
            getEntityTypeName(
                node.type
            );


        const designation =
            getEntityDesignation(
                node.type,
                node.id
            );


        const employeeCode =
            getEntityCode(
                node.type,
                node.id
            );


        const icon =
            getEntityIcon(
                node.type
            );


        let underText = '';


        if (
            node.relation
        ) {

            underText =
                'Under: ' +

                getEntityName(
                    node.relation.parent_type,
                    node.relation.parent_id
                );

        }


        const rootBadge =
            node.level === 0 ?
            `

                <span class="badge badge-primary ml-2">
                    Root
                </span>

            ` :
            '';


        const typeBadgeClass =
            typeName === 'Dealer' ?
            'badge-warning' :
            typeName === 'Agent' ?
            'badge-secondary' :
            'badge-info';


        const nodeHtml = $(

            `

        <div
            class="marketing-chain-node"
            style="margin-left:${node.level * 40}px;"
        >

            ${
                node.level > 0
                    ?
                    `

                        <div class="mb-1 text-muted small">

                            <i class="fas fa-level-down-alt"></i>

                            ${underText}

                        </div>

                    `
                    :
                    ''
            }


            <div
                class="card shadow-sm mb-2"
                style="border-left:4px solid #6777ef;"
            >

                <div class="card-body py-3">

                    <div class="d-flex align-items-center">

                        <div
                            class="mr-3 d-flex align-items-center justify-content-center"
                            style="
                                width:42px;
                                height:42px;
                                border-radius:50%;
                                background:#f1f3f8;
                            "
                        >

                            <i
                                class="fas ${icon} text-primary"
                            ></i>

                        </div>


                        <div class="flex-grow-1">

                            <div class="d-flex align-items-center flex-wrap">

                                <strong>
                                    ${name}
                                </strong>

                                ${rootBadge}

                            </div>


                            <div class="text-muted small mt-1">

                                ${
                                    designation
                                        ?
                                        designation +
                                        ' | '
                                        :
                                        ''
                                }

                                ${
                                    employeeCode
                                        ?
                                        employeeCode +
                                        ' | '
                                        :
                                        ''
                                }


                                <span class="badge ${typeBadgeClass}">
                                    ${typeName}
                                </span>

                            </div>

                        </div>


                        <div class="text-right">

                            <div>

                                <span class="badge badge-light">
                                    Level ${node.level}
                                </span>

                            </div>

                            <div class="mt-1">

                                <span class="badge badge-success">
                                    Active
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        `

        );


        container.append(
            nodeHtml
        );


        if (
            node.children &&
            node.children.length
        ) {

            node.children.forEach(function(child) {

                renderChainNode(
                    child,
                    container
                );

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RENDER MARKETING CHAIN
    |--------------------------------------------------------------------------
    */

    function renderMarketingChain() {
        const container =
            $('#marketingChain');


        container.empty();


        const trees =
            buildLiveChain();


        if (!trees.length) {

            container.html(`

            <div class="text-center py-5">

                <div class="mb-3">

                    <i
                        class="fas fa-sitemap"
                        style="
                            font-size:48px;
                            color:#d1d5db;
                        "
                    ></i>

                </div>

                <h6 class="text-muted">
                    No active marketing relations found.
                </h6>

                <p class="text-muted mb-0">
                    Add a marketing relation to build the hierarchy.
                </p>

            </div>

        `);

            return;

        }


        trees.forEach(function(tree) {

            renderChainNode(
                tree,
                container
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | PARENT TYPE CHANGE
    |--------------------------------------------------------------------------
    */

    $('#parentType').on(
        'change',
        function() {

            populateParents();


            $('#childType').html(
                '<option value="">Select Child Type</option>'
            );


            $('#childId').html(
                '<option value="">Select Child</option>'
            );


            $('#parentDesignation').val('');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PARENT CHANGE
    |--------------------------------------------------------------------------
    */

    $('#parentId').on(
        'change',
        function() {

            const type =
                Number(
                    $('#parentType').val()
                );


            const id =
                $(this).val();


            $('#parentDesignation').val(

                getEntityDesignation(
                    type,
                    id
                )

            );


            populateChildTypes();


            $('#childId').html(
                '<option value="">Select Child</option>'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CHILD TYPE CHANGE
    |--------------------------------------------------------------------------
    */

    $('#childType').on(
        'change',
        function() {

            populateChildren();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ADD RELATION
    |--------------------------------------------------------------------------
    */

    $('#addRelation').on(
        'click',
        function() {

            const parentType =
                Number(
                    $('#parentType').val()
                );


            const parentId =
                $('#parentId').val();


            const childType =
                Number(
                    $('#childType').val()
                );


            const childId =
                $('#childId').val();


            if (
                !parentType ||
                !parentId ||
                !childType ||
                !childId
            ) {

                showMessage(
                    'warning',
                    'Please select parent and child properly.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Same entity protection
            |--------------------------------------------------------------------------
            */

            if (
                parentType === childType &&
                String(parentId) ===
                String(childId)
            ) {

                showMessage(
                    'warning',
                    'Parent and child cannot be the same.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Duplicate protection
            |--------------------------------------------------------------------------
            */

            if (
                relationExists(
                    parentType,
                    parentId,
                    childType,
                    childId
                )
            ) {

                showMessage(
                    'warning',
                    'This relation already exists.'
                );

                return;

            }


            const button =
                $(this);


            button
                .prop(
                    'disabled',
                    true
                )
                .html(
                    '<i class="fas fa-spinner fa-spin"></i> Saving...'
                );


            $.ajax({

                url: storeUrl,

                method: 'POST',

                data: {

                    _token: csrfToken,

                    parent_type: parentType,

                    parent_id: parentId,

                    child_type: childType,

                    child_id: childId

                },


                success: function(response) {

                    /*
                    |--------------------------------------------------------------------------
                    | Add response to live data
                    |--------------------------------------------------------------------------
                    */

                    relationsData.push({

                        id: response.relation_id,

                        parent_type: Number(
                            response.parent_type
                        ),

                        parent_id: Number(
                            response.parent_id
                        ),

                        parent_name: response.parent_name,

                        parent_entity_type: response.parent_entity_type,

                        child_type: Number(
                            response.child_type
                        ),

                        child_id: Number(
                            response.child_id
                        ),

                        child_name: response.child_name,

                        child_entity_type: response.child_entity_type,

                        status: Number(
                            response.status
                        )

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh UI without reload
                    |--------------------------------------------------------------------------
                    */

                    renderRelationsTable();

                    renderMarketingChain();


                    showMessage(
                        'success',
                        response.message ||
                        'Marketing relation added successfully.'
                    );


                    $('#childId').val('');

                },


                error: function(xhr) {

                    let message =
                        'Unable to add marketing relation.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    showMessage(
                        'danger',
                        message
                    );

                },


                complete: function() {

                    button
                        .prop(
                            'disabled',
                            false
                        )
                        .html(
                            '<i class="fas fa-plus"></i> Add Relation'
                        );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CANCEL RELATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.cancel-relation',
        function() {

            const button =
                $(this);


            const relationId =
                button.data(
                    'relation-id'
                );


            if (!relationId) {

                return;

            }


            if (
                !confirm(
                    'Are you sure you want to cancel this relation?'
                )
            ) {

                return;

            }


            button
                .prop(
                    'disabled',
                    true
                )
                .html(
                    '<i class="fas fa-spinner fa-spin"></i>'
                );


            $.ajax({

                url: cancelUrl +
                    '/' +
                    relationId +
                    '/cancel',

                method: 'PATCH',

                data: {

                    _token: csrfToken

                },


                success: function(response) {

                    const relation =
                        relationsData.find(
                            function(item) {

                                return String(
                                        item.id
                                    ) ===
                                    String(
                                        relationId
                                    );

                            }
                        );


                    if (relation) {

                        relation.status =
                            0;

                    }


                    renderRelationsTable();

                    renderMarketingChain();


                    showMessage(
                        'success',
                        response.message ||
                        'Marketing relation cancelled successfully.'
                    );

                },


                error: function(xhr) {

                    let message =
                        'Unable to cancel relation.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    showMessage(
                        'danger',
                        message
                    );


                    button
                        .prop(
                            'disabled',
                            false
                        )
                        .html(
                            '<i class="fas fa-times"></i>'
                        );

                }

            });

        }
    );




    $(document).ready(
        function() {

            populateParentTypes();

            renderRelationsTable();

            renderMarketingChain();

        }
    );
</script>

@endpush