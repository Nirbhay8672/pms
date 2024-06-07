<template>
    <inertia-head title="Dashboard" />
    <main-page>
        <h5>Dashboard</h5>

        <div class="row mt-4">
            <div class="d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-12 col-lg-3 col-md-6 col-sm-6" v-if="hasPermission('view_websites')">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Websites</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-list"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">
                                        {{ $page.props.total_websites }}
                                    </h1>
                                </div>
                                <div class="card-footer text-center">
                                    <a
                                        class="text-primary"
                                        :href="`${$page.props.url}/websites/index`"
                                        style="text-decoration: none"
                                        >More</a
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-6 col-sm-6" v-if="hasPermission('view_clients')">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Clients</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">
                                        {{ $page.props.total_clients }}
                                    </h1>
                                </div>
                                <div class="card-footer text-center">
                                    <a
                                        class="text-primary"
                                        :href="`${$page.props.url}/clients/index`"
                                        style="text-decoration: none"
                                        >More</a
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-6 col-sm-6" v-if="hasPermission('view_users')">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Users</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">
                                        {{ $page.props.total_users }}
                                    </h1>
                                </div>
                                <div class="card-footer text-center">
                                    <a
                                        class="text-primary"
                                        :href="`${$page.props.url}/users/index`"
                                        style="text-decoration: none"
                                        >More</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table
                                class="table"
                                style="
                                    border-collapse: separate;
                                    border-spacing: 0 10px;
                                "
                            >
                                <thead>
                                    <tr class="custom-table-heading">
                                        <th>Plugin Name</th>
                                        <th>Is Active</th>
                                        <th>Version</th>
                                        <th>Author</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="details_fetch">
                                        <tr>
                                            <td>{{ plugin_details.plugin_name }}</td>
                                            <td>
                                                <i :class="plugin_details.is_active == 1 ? 'fa fa-check-circle text-success' :  'fa fa-times-circle text-danger'"></i>
                                            </td>
                                            <td>{{ plugin_details.version }}</td>
                                            <td>{{ plugin_details.author }}</td>
                                            <td class="text-center">
                                                <button
                                                    class="btn btn-success btn-sm"
                                                    v-if="plugin_details.is_active == 0"
                                                    @click="activeOrDeactive(1)"
                                                >Active</button>

                                                <button
                                                    class="btn btn-warning btn-sm"
                                                    v-if="plugin_details.is_active == 1"
                                                    @click="activeOrDeactive(0)"
                                                >Deactive</button>
                                                
                                                <button
                                                    class="btn btn-danger btn-sm ms-2"
                                                    @click="deletePlugin()"
                                                >Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr>
                                            <td colspan="5" class="text-center">Not Found ....</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-3">
                                <input type="file" id="zip_file" accept=".zip" class="form-control">
                                <button
                                    class="btn btn-primary btn-sm mt-3"
                                    @click="updatePluginFiles()"
                                >
                                    <span class="ms-2">Update Plugin</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main-page>
</template>

<script setup>
import axios from 'axios';
import { pluginRoutes } from '../routes/PluginRoutes';
import { confirmAlert, toastAlert } from '../helpers/alert';
import { onMounted, reactive, ref } from 'vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    getPluginDetails(); 
});

let details_fetch = ref(false);

let plugin_details = reactive({
    plugin_name : "",
    is_active : "",
    version : "",
    author : "",
});

function getPluginDetails() {
    
    details_fetch.value = false;

    axios.get(pluginRoutes.pluginDetails)
        .then((response) => {

            details_fetch.value = true;

            let data = response.data.data; 
            
            plugin_details.plugin_name = data.Name;
            plugin_details.is_active = data.is_active ? 1 : 0;
            plugin_details.version = data.Version;
            plugin_details.author = data.Author;
        });
}

function updatePluginFiles() {
    let form_data = new FormData(); 

    let zip_file = document.getElementById("zip_file");

    if (zip_file && zip_file.files.length > 0) {

        let file = zip_file.files[0];
        form_data.set("zip_file", file, file.name);

        let settings = { headers: { "content-type": "multipart/form-data" } };

        axios
            .post(pluginRoutes.updatePlugin, form_data, settings)
            .then((response) => {
                toastAlert({ title: response.data.message });
                getPluginDetails();
            })
            .catch(function (error) {
                toastAlert({
                    title: error.response.data.message,
                    icon: "error",
                });
            });
    }
}

function activeOrDeactive(status) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to ${status == 1 ? 'Active' : 'Deactive' } <strong> ${plugin_details.plugin_name} </strong> plugin ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .post(pluginRoutes.activeOrDeactive, {
                    'status' : status,
                })
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    getPluginDetails();
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

function deletePlugin() {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: "Are you sure, you want to delete this plugin ?",
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(pluginRoutes.delete)
            .then((response) => {
                toastAlert({ title: 'plugin delete successfully.' });
                getPluginDetails();
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
