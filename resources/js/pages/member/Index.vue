<template>
    <inertia-head title="Members" />
    <main-page>
        <div class="container-fluid p-0 mb-3">
            <div class="row mb-2 gy-3">
                <div class="col-sm-6">
                    <h5 class="d-inline align-middle">Members</h5>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end gy-3">
                        <button class="btn btn-info btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-sm-2 mt-3 mt-md-0 mt-lg-0 mt-sm-0"
                            @click="updatePlugin()" v-if="selected_members.length > 0">
                            <i class="fa fa-wrench"></i>
                            <span class="ms-2">Update Plugin</span>
                        </button>
                        <button
                            class="btn btn-danger btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-sm-2 mt-3 mt-md-0 mt-lg-0 mt-sm-0"
                            @click="deleteMembers()" v-if="selected_members.length > 0">
                            <i class="fa fa-trash"></i>
                            <span class="ms-2">Delete</span>
                        </button>
                        <button
                            class="btn btn-primary btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-sm-2 mt-3 mt-md-0 mt-lg-0 mt-sm-0"
                            @click="openForm()" v-if="hasPermission('add_member')">
                            <i class="fa fa-plus-circle"></i>
                            <span class="ms-2">Add</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4" v-if="loader" style="height: 200px">
                        <div class="overflow dark" id="preload">
                            <div class="circle-line">
                                <div class="circle-red"><b>P</b></div>
                                <div class="circle-blue"><b>M</b></div>
                                <div class="circle-red"><b>S</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" v-else>
                        <div class="row mt-2 justify-content-between gy-3 mb-3">
                            <div class="col-md-auto me-auto">
                                <div class="dt-length">
                                    <select class="form-select form-control" id="per_page" v-model="fields.per_page"
                                        @change="changeMainFilter()">
                                        <option value="5">5</option>
                                        x
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <div class="dt-search">
                                    <input type="text" id="search_input" placeholder="Search..." class="form-control"
                                        v-model="fields.search" @keyup="changeMainFilter()" />
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: separate; border-spacing: 0 10px">
                                    <thead>
                                        <tr class="custom-table-heading">
                                            <th>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" @click="checkAll"
                                                        id="check_all" :checked="members.length == selected_members.length &&
                                                                selected_members.length > 0
                                                                ? true
                                                                : false
                                                            " />
                                                </div>
                                            </th>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Website Link</th>
                                            <th>Licence Key</th>
                                            <th>User Status</th>
                                            <th>Plugin Version</th>
                                            <th>Send Update</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="members.length > 0">
                                            <tr v-for="(member, index) in members" :key="`member_${index}`">
                                                <td style="min-width: 10px">
                                                    <div class="form-check">
                                                        <input class="form-check-input" v-model="selected_members"
                                                            type="checkbox" :id="`member_${member.id}`"
                                                            :value="member.id" />
                                                    </div>
                                                </td>
                                                <td style="min-width: 50px">
                                                    {{ index + 1 }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    {{ member.username }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    {{ member.email }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    {{ member.phone_number }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    <a :href="member.website_link" target="_blank"
                                                        style="text-decoration: none">{{ member.website_name }}</a>
                                                </td>
                                                <td style="width: 100px">
                                                    {{ member.licence_key }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            :id="`switch_${member.id}`"
                                                            :checked="member.user_status > 0 ? true : false" />
                                                    </div>
                                                </td>
                                                <td style="min-width: 200px">
                                                    {{ member.plugin_version }}

                                                    <template v-if="member.plugin_version">
                                                        <button class="btn btn-success btn-sm ms-3"
                                                            v-if="member.plugin_is_active == 0"
                                                            @click="activeOrDeactive(member,1)">
                                                            Active
                                                        </button>

                                                        <button class="btn btn-warning btn-sm ms-3"
                                                            v-if="member.plugin_is_active == 1"
                                                            @click="activeOrDeactive(member,0)">
                                                            Deactive
                                                        </button>
                                                    </template>

                                                    <button class="btn btn-outline-danger btn-sm ms-3"
                                                        v-if="member.plugin_version" @click="deletePlugin(member)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                                <td style="min-width: 100px" class="text-center">
                                                    <i :class="member.send_update > 0 ? 'fa fa-check' : 'fa fa-envelope'
                                                        "></i>
                                                </td>
                                                <td style="min-width: 160px" class="text-center">
                                                    <button class="btn btn-outline-primary btn-sm ms-3"
                                                        @click="openForm(member)" v-if="hasPermission('update_member')">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button class="btn btn-outline-danger btn-sm ms-3"
                                                        @click="deleteMember(member)"
                                                        v-if="hasPermission('delete_member')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                                    <button class="btn btn-outline-success btn-sm ms-3"
                                                        @click="openModalForUpdatePackage(member)">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr style="width: 100%" class="text-center">
                                                <td colspan="12">
                                                    <span class="text-center text-muted">No Members Found</span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row gy-3" v-if="members.length > 0">
                            <div class="col-md-auto me-auto">
                                <div>
                                    Showing {{ fields.start_index }} to {{ fields.end_index }} of
                                    {{ fields.total_record }} Results
                                </div>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <div class="dt-paging paging_full_numbers">
                                    <ul class="pagination">
                                        <li class="page-item" @click="prev()">
                                            <span class="page-link"><i class="fa fa-angle-double-left"></i></span>
                                        </li>
                                        <template v-for="page_number in fields.total_pages"
                                            :key="`page_number_${page_number}`">
                                            <li class="page-item" :class="page_number === fields.page ? 'active' : ''"
                                                @click="setPage(page_number)">
                                                <span class="page-link cursor-pointer">{{ page_number }}</span>
                                            </li>
                                        </template>

                                        <li class="page-item" @click="next()">
                                            <span class="page-link"><i class="fa fa-angle-double-right"></i></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <teleport to="body">
            <member-form ref="member_form" @reload="reloadTable()"></member-form>
            <update-plugin-form ref="update_plugin_form" :auth="auth" @reload="reloadTable()" />
        </teleport>
    </main-page>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import { toastAlert, confirmAlert } from "../../helpers/alert";
import { memberRoutes } from "../../routes/MemberRoutes";
import memberForm from "./includes/Form.vue";
import updatePluginForm from "./includes/UpdatePluginForm.vue";
import { pluginRoutes } from "../../routes/PluginRoutes";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let members = ref([]);
let loader = ref(true);
let selected_members = ref([]);
let update_plugin_form = ref(null);

let member_form = ref(null);

let fields = reactive({
    search: "",
    per_page: 10,
    total_record: 0,
    total_pages: 1,
    page: 1,
    start_index: 0,
    end_index: 0,
});

onMounted(() => {
    setTimeout(function () {
        reloadTable();
    }, 1000);
});

function changeMainFilter() {
    fields.page = 1;
    reloadTable();
}

function setPage(page_number = 1) {
    fields.page = page_number;
    reloadTable();
}

function prev() {
    if (fields.page === 1) {
        return;
    }
    fields.page--;
    reloadTable();
}

function next() {
    if (fields.page === fields.total_pages) {
        return;
    }
    fields.page++;
    reloadTable();
}

function openForm(member = null) {
    member_form.value.openModal(member);
}

function checkAll(event) {
    if (event.target.checked) {
        members.value.forEach((member) => {
            selected_members.value.push(member.id);
        });
    } else {
        selected_members.value = [];
    }
}

function reloadTable() {
    axios
        .post(memberRoutes.datatable, fields)
        .then((response) => {
            members.value = response.data.members;
            fields.total_record = response.data.total;
            fields.total_pages = response.data.total_pages;
            fields.start_index = response.data.start_index;
            fields.end_index = response.data.end_index;
            loader.value = false;
        })
        .catch(function (error) {
            if (error.response.status === 422) {
                toastAlert({
                    title: "somthing went wrong.",
                    icon: "error",
                });
            }
        });
}

function openModalForUpdatePackage(member) {
    update_plugin_form.value.openModal(member);
}

function deletePlugin(member) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: "Are you sure want to delete plugin from live site ?",
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .get(pluginRoutes.delete(member.id))
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    reloadTable();
                })
                .catch(function (error) {
                    if (error.response.status === 404) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
                });
        }
    });
}

function deleteMember(member) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to delete <strong> ${member.username} </strong> member ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .get(memberRoutes.deleteMember(member.id))
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    reloadTable();
                })
                .catch(function (error) {
                    if (error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
                });
        }
    });
}

function updatePlugin() {
    confirmAlert({
        title: "Update Plugin",
        icon: "question",
        html: "Are you sure want to update plugin on selected websites ?",
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(pluginRoutes.bulkPluginUpdate, {
                    selected_members: selected_members.value ?? [],
                })
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    selected_members.value = [];
                    reloadTable();
                })
                .catch(function (error) {
                    if (error.response.status === 404) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
                });
        }
    });
}

function activeOrDeactive(member , status) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to ${status == 1 ? 'Active' : 'Deactive' } plugin ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(pluginRoutes.activeOrDeactive, {
                    'status' : status,
                    'member_id' : member.id,
                })
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    reloadTable();
                })  
                .catch(function (error) {
                    if (error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
                });
        }
    });
}

function deleteMembers() {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: "Are you sure want to delete all members ?",
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(memberRoutes.deleteMembers, {
                    selected_members: selected_members.value ?? [],
                })
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    selected_members.value = [];
                    reloadTable();
                })
                .catch(function (error) {
                    if (error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
                });
        }
    });
}

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}
</script>
