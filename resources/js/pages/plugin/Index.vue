<template>
    <inertia-head title="Plugin" />
    <main-page>
        <div class="container-fluid p-0 mb-3">
            <div class="row mb-2 gy-3">
                <div class="col-sm-6">
                    <h5 class="d-inline align-middle">Plugin</h5>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-body p-4"
                        v-if="loader"
                        style="height: 200px"
                    >
                        <div class="pre-loader" id="preload">
                            <div class="circle-line">
                                <div class="circle-red"><b>P</b></div>
                                <div class="circle-blue"><b>M</b></div>
                                <div class="circle-red"><b>S</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" v-else>
                        <div class="card-body">
                            <h5 class="mb-4">Set Default Plugin File</h5>

                            <div class="row mt-2 mb-4" v-if="exist_file">
                                <div class="col">
                                    <b class="me-2">Default File : </b> {{ exist_file.file_name }}
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-3">
                                    <input
                                        type="file"
                                        id="zip_file"
                                        ref="zip_file"
                                        @change="setFile"
                                        class="form-control"
                                        accept=".zip"
                                        :class="{
                                            'is-invalid':
                                                formValidation.hasError('zip_file'),
                                        }"
                                    />
                                    <span
                                        :class="{
                                            'is-invalid':
                                                formValidation.hasError('zip_file'),
                                        }"
                                    ></span>
                                    <div
                                        class="invalid-feedback"
                                        v-if="formValidation.hasError('zip_file')"
                                    >
                                        <span>{{
                                            formValidation.getError("zip_file")[0]
                                        }}</span>
                                    </div>
                                    <button
                                        class="btn btn-primary btn-sm mt-4"
                                        @click="handleSubmit()"
                                    >Save
                                    </button>
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
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import { FormValidation } from "../../helpers/Validation";
import { pluginRoutes } from "../../routes/PluginRoutes";
import { toastAlert } from "../../helpers/alert";
import { resetObjectKeys } from "../../helpers/utils";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
    plugin_details : {},
});

let loader = ref(true);

let exist_file = ref(null);

let fields = reactive({
    id : '',
    zip_file : '',
});

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);

    fields.id = props.plugin_details ? props.plugin_details.id : '';
    exist_file.value = props.plugin_details;
});

function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
    document.getElementById('zip_file').value = "";
}

function setFile(event) {
    fields.zip_file = event.target.files[0].name;
}

function handleSubmit() {
    formValidation.validate();

    let form_data = new FormData();
    let zip_file = document.getElementById("zip_file");

    if (zip_file && zip_file.files.length > 0) {
        let file = zip_file.files[0];
        form_data.set("zip_file", file, file.name);
    }

    form_data.set("id", fields.id);

    let settings = { headers: { "content-type": "multipart/form-data" } };

    if (formValidation.isValid()) {
        axios
            .post(pluginRoutes.setDefaultPlugin, form_data, settings)
            .then((response) => {
                toastAlert({ title: response.data.message });
                exist_file.value = response.data.plugin_details;
                fields.id = response.data.plugin_details.id;
                clearFormData();
            })
            .catch(function (error) {
                if (error.response.status === 422) {
                    formValidation.setServerSideErrors(
                        error.response.data.errors
                    );
                }
            });
    }
}

let formValidation = reactive(
    new FormValidation(fields, {
        zip_file: {
            required: "The plugin file is required.",
        },
    })
);

</script>
