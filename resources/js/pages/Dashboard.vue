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
                                            <h5 class="card-title">Meshulam Plugin</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">
                                        {{ $page.props.total_members }}
                                    </h1>
                                </div>
                                <div class="card-footer text-center">
                                    <a
                                        class="text-primary"
                                        :href="`${$page.props.url}/members/index`"
                                        style="text-decoration: none"
                                        >More</a
                                    >
                                </div>
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

let plugin_details = reactive({
    plugin_name : "",
    is_active : "",
    version : "",
    author : "",
});

function getPluginDetails() {

    axios.get(pluginRoutes.pluginDetails)
        .then((response) => {
        });
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
