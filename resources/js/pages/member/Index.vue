<template>
    <inertia-head title="Members" />
    <main-page>
        <div class="row">
            <div class="col-12">
                <div class="card" v-if="loader" style="height: 200px">
                    <div class="card-body p-4">
                        <div class="pre-loader" id="preload">
                            <div class="circle-line">
                                <div class="circle-red"></div>
                                <div class="circle-blue"></div>
                                <div class="circle-red"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" v-else>
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">All Members</h5>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">

                                    <button
                                        class="btn bg-gradient-info mt-auto mb-0 ms-2"
                                        type="button"
                                        @click="updatePlugin()" v-if="selected_members.length > 0">
                                        <i class="fa fa-wrench"></i>
                                        <span class="ms-2">Update Plugin</span>
                                    </button>
                                    
                                    <button
                                        class="btn bg-gradient-danger mt-auto mb-0 ms-2"
                                        type="button"
                                        @click="deletePlugins()" v-if="selected_members.length > 0">
                                        <i class="fa fa-trash"></i>
                                        <span class="ms-2">Delete Plugin</span>
                                    </button>
                        
                                    <button
                                        class="btn bg-gradient-secondary btn-icon-only mt-auto mb-0 ms-2"
                                        type="button"
                                        name="button"
                                        @click="openForm()"
                                        v-if="hasPermission('add_member')"
                                    ><i class="material-icons text-sm">add</i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive">
                            <div
                                class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <div class="dataTable-top">
                                    <div class="dataTable-dropdown">
                                        <label>
                                            <select class="dataTable-selector me-2" id="per_page" v-model="fields.per_page" @change="changeMainFilter()">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="15">15</option>
                                                <option value="20">20</option>
                                                <option value="25">25</option>
                                            </select>
                                            Entries per page
                                        </label>
                                    </div>
                                    <div class="dataTable-search">
                                        <input
                                            class="dataTable-input"
                                            placeholder="Search..."
                                            type="text"
                                            id="search_input"
                                            v-model="fields.search"
                                            @keyup="changeMainFilter()"
                                        >
                                    </div>
                                </div>
                                <div class="dataTable-container">
                                    <table class="table table-flush dataTable-table" id="member_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>
                                                    <div class="form-check my-auto check-all">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            id="check_all"
                                                            :checked="members.length == selected_members.length &&
                                                                selected_members.length > 0
                                                                ? true
                                                                : false"
                                                            @click="checkAll"
                                                        />
                                                    </div>
                                                </th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Website Link</th>
                                                <th>Licence Key</th>
                                                <th>User Status</th>
                                                <th>Plugin Version</th>
                                                <th>Send Update</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="members.length > 0">
                                                <tr v-for="(member, index) in members" :key="`member_${index}`" class="text-sm">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="selected_members"
                                                                type="checkbox" :id="`member_${member.id}`"
                                                                :value="member.id" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ member.username }}
                                                    </td>
                                                    <td>
                                                        {{ member.email }}
                                                    </td>
                                                    <td>
                                                        {{ member.phone_number }}
                                                    </td>
                                                    <td>
                                                        <a :href="member.website_link" target="_blank" style="text-decoration: none">{{ member.website_name }}</a>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="licence-key d-inline-block mt-2" :id="`licence_key_${index}`">{{ member.licence_key }}</div>
                                                            </div>
                                                            <div class="col">
                                                                <button
                                                                    class="btn btn-outline-primary btn-icon-only btn-sm d-inline-block"
                                                                    @click="copyLicenceKey(`licence_key_${index}`)"
                                                                >
                                                                    <i
                                                                        class="fa fa-copy"
                                                                        style="font-size: 12px;"
                                                                    ></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch"
                                                                :id="`switch_${member.id}`"
                                                                @change="updateUserStatus(member.id, member.user_status)"
                                                                :checked="member.user_status > 0 ? true : false" />
                                                            <span class="ms-2"
                                                                :class="member.user_status > 0 ? 'text-success' : 'text-danger'">{{
                                                                    member.user_status > 0 ? 'Active' : 'Dective' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span v-if="member.plugin_version">
                                                            <b>{{ member.plugin_version }}</b>
                                                        </span>
                                                        <span v-else="" class="text-danger">Not Installed</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <i :class="member.send_update > 0 ? 'fa fa-check' : 'fa fa-envelope'
                                                            "></i>
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-outline-info btn-icon-only btn-sm"
                                                            title="Edit Details"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            type="button"
                                                            @click="openForm(member)"
                                                            v-if="hasPermission('update_member')"
                                                        >
                                                            <i class="fa fa-pencil" style="font-size: 12px;"></i>
                                                        </button>

                                                        <button
                                                            class="btn btn-outline-warning btn-icon-only ms-2 btn-sm"
                                                            title="Upload Plugin"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            type="button"
                                                            @click="openModalForUpdatePackage(member)"
                                                        >
                                                            <i class="fa fa-upload" style="font-size: 12px;"></i>
                                                        </button>

                                                        <button
                                                            class="btn btn-outline-danger btn-icon-only ms-2 btn-sm"
                                                            title="Delete Plugin"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            type="button"
                                                            v-if="member.plugin_version" @click="deletePlugin(member)"
                                                        >
                                                            <i class="fa fa-trash" style="font-size: 12px;"></i>
                                                        </button>

                                                        <template v-if="member.plugin_version">
                                                            <button
                                                                class="btn btn-outline-success btn-icon-only ms-2 btn-sm"
                                                                title="Active Plugin"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                type="button"
                                                                v-if="member.plugin_is_active == 0"
                                                                @click="activeOrDeactive(member, 1)"
                                                            ><i class="fa fa-check" style="font-size: 12px;"></i>
                                                            </button>

                                                            <button
                                                                class="btn btn-outline-danger btn-icon-only ms-2 btn-sm"
                                                                title="Deactive Plugin"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                type="button"
                                                                v-if="member.plugin_is_active == 1"
                                                                @click="activeOrDeactive(member, 0)"
                                                            ><i class="fa fa-times" style="font-size: 12px;"></i>
                                                            </button>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr class="text-center">
                                                    <td colspan="10">
                                                        <span class="text-center text-muted">No Members Found</span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="dataTable-bottom" v-if="members.length > 0">
                                    <div class="dataTable-info">Showing {{ fields.start_index }} to {{ fields.end_index }} of {{ fields.total_record }} Results</div>
                                    <nav class="dataTable-pagination">
                                        <ul class="dataTable-pagination-list">
                                            <li class="pager">
                                                <button type="button" class="btn btn-outline-primary btn-icon-only rounded-circle" @click="prev()">
                                                    <span class="btn-inner--icon"><i class="fa fa-angle-double-left fs-6"></i></span>
                                                </button>
                                            </li>
                                            <template v-for="page_number in fields.total_pages"
                                                :key="`page_number_${page_number}`">
                                                <li class="paper ms-2">
                                                    <button
                                                        type="button"
                                                        class="btn btn-icon-only rounded-circle"
                                                        :class="page_number === fields.page ? 'btn btn-primary' : 'btn btn-outline-primary'"
                                                        @click="setPage(page_number)"
                                                    >
                                                        <span class="btn-inner--icon">{{ page_number }}</span>
                                                    </button>
                                                </li>
                                            </template>
                                            <li class="pager ms-2">
                                                <button type="button" class="btn btn-outline-primary btn-icon-only rounded-circle" @click="next()">
                                                    <span class="btn-inner--icon"><i class="fa fa-angle-double-right fs-6"></i></span>
                                                </button>
                                            </li>
                                        </ul>
                                    </nav>
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
import { ref, onMounted, reactive, nextTick } from "vue";
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

            nextTick(() => {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
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

function copyLicenceKey(licence_key_id) {
    let textToCopy = document.getElementById(licence_key_id);

    let selection = window.getSelection();
    let range = document.createRange();

    range.selectNodeContents(textToCopy);
    selection.removeAllRanges();
    selection.addRange(range);

    document.execCommand('copy');
    selection.removeAllRanges();

    toastAlert({ title: 'key Copied ...' });
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

function deletePlugins() {
    confirmAlert({
        title: "Delete Plugin",
        icon: "question",
        html: "Are you sure want to delete plugin from selected websites ?",
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(pluginRoutes.bulkPluginDelete, {
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

function updateUserStatus(member_id, user_status) {
    confirmAlert({
        title: "Update User Status",
        icon: "question",
        html: `Are you sure want to ${user_status == 1 ? 'Deactive' : 'Active'} this user ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(memberRoutes.updateUserStatus, {
                    'member_id': member_id,
                    'user_status': user_status,
                })
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

function activeOrDeactive(member, status) {
    confirmAlert({
        title: "Active Or Deactive",
        icon: "question",
        html: `Are you sure, you want to ${status == 1 ? 'Active' : 'Deactive'} plugin ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(pluginRoutes.activeOrDeactive, {
                    'status': status,
                    'member_id': member.id,
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

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}
</script>
