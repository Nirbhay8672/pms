<template>
    <Modal ref="plugin_upload_form" :id="'plugin_upload_form'">
        <template #modal_title>
            <span>Update Plugin For : {{ site_name }}</span>
        </template>

        <form class="form">
            <div class="row mb-3 gy-3">
                <div class="col">
                    <div class="file-upload">
                        <input
                            type="file"
                            id="zip_file"
                            class="file-input"
                            @change="setFile"
                            accept=".zip"
                        />
                        <label for="zip_file" class="file-label">
                            <span class="file-label-icon">📁</span>
                            <span class="file-label-text">Choose a file...</span>
                        </label>
                        <span class="file-name text-center" id="fileName">No file chosen</span>
                        <div
                            class="text-danger text-center"
                            v-if="formValidation.hasError('zip_file')"
                        >
                            <small>{{
                                formValidation.getError("zip_file")[0]
                            }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <template #modal_footer>
            <button class="btn btn-primary btn-sm" type="button" @click="handleSubmit">
                Submit
            </button>
        </template>
    </Modal>
</template>

<script setup>
import axios from "axios";
import { ref, reactive } from "vue";
import Modal from "../../../components/Modal.vue";
import { FormValidation } from "../../../helpers/Validation";
import { pluginRoutes } from "../../../routes/PluginRoutes";
import { toastAlert } from "../../../helpers/alert";

let plugin_upload_form = ref(null);

const emits = defineEmits(["reload"]);

let site_name = ref('');

let fields = reactive({
    id: "",
    zip_file: "",
});

function openModal(member) {
    clearFormData();
    plugin_upload_form.value.open();

    fields.id = member.id;
    site_name.value = member.website_name;
}

function clearFormData() {
    formValidation.reset();
    fields.zip_file = "";
    fields.id = "";

    let zip_file = document.getElementById('zip_file');
    zip_file.value = '';
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
            .post(pluginRoutes.updatePlugin, form_data, settings)
            .then((response) => {
                if(response.data.status === 200) {
                    plugin_upload_form.value.close();
                    toastAlert({ title: response.data.message });
                    emits('reload');
                    clearFormData();
                }

                if(response.data.status === 403 || response.data.status === 404) {
                    toastAlert({ icon : 'error', title: response.data.message });
                }
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

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        zip_file: {
            required: "The plugin file is required.",
        },
    })
);
</script>
