<template>
    <inertia-head title="Dashboard" />
    <main-page>
        <h5>Dashboard</h5>

        <div class="row mt-4">
            <div class="d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-12 col-lg-3 col-md-6 col-sm-6" v-if="hasPermission('view_members')">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Otech Plugins</h5>
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
const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

</script>
