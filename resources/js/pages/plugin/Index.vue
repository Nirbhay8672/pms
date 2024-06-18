<template>
    <inertia-head title="Plugin" />
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
                    <div class="card-body">
                        <div class="row mt-2 mb-4 text-center">
                            <div class="col">
                                <b class="me-2">Default Plugin</b>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="d-flex mt-n2">
                                    <div class="avatar avatar-xl bg-gradient-light border-radius-xl p-2 mt-n4">
                                        <img :src="`${$page.props.url}/images/wordpress.png`" alt="asana_logo">
                                    </div>
                                    <div class="ms-3 my-auto">
                                        <h6 class="mb-0">Default Plugin File</h6>
                                    </div>
                                </div>
                                <p class="text-sm mt-3"> This is default plugin file for bulk update </p>
                                <hr class="horizontal dark">
                                <div class="row mt-2 mb-4" v-if="exist_file">
                                    <div class="col">
                                        <b class="me-2">Default Plugin File : </b> {{ exist_file.file_name }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <div class="file-upload">
                                            <input type="file" id="zip_file" class="file-input" @change="setFile"
                                                accept=".zip" />
                                            <label for="zip_file" class="file-label">
                                                <span class="file-label-icon">📁</span>
                                                <span class="file-label-text">Choose a file...</span>
                                            </label>
                                            <span class="file-name" style="margin-left: 8px;" id="fileName">No file chosen</span>
                                            <div class="text-danger" style="margin-left: 8px;" v-if="formValidation.hasError('zip_file')">
                                                <small>{{
                                                    formValidation.getError("zip_file")[0]
                                                    }}</small>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary btn-sm mt-3" type="button" @click="handleSubmit">
                                            Save
                                        </button>
                                    </div>
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
    plugin_details: {},
});

let loader = ref(true);

let exist_file = ref(null);

let fields = reactive({
    id: '',
    zip_file: '',
});

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);

    fields.id = props.plugin_details ? props.plugin_details.id : '';
    fields.zip_file = '';
    exist_file.value = props.plugin_details;
});

function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
    document.getElementById('zip_file').value = "";
    document.getElementById('fileName').textContent = 'No file chosen';
}

function setFile(event) {
    fields.zip_file = event.target.files[0].name;

    const fileName = event.target.files[0] ? event.target.files[0].name : 'No file chosen';
    document.getElementById('fileName').textContent = fileName;
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
