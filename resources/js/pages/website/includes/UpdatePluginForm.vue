<template>
    <Modal ref="plugin_upload_form" :id="'plugin_upload_form'">
        <template #modal_title>
            <span>Update Plugin</span>
        </template>

        <form>
            <div class="row mb-3 gy-3">
                <div class="col-12">
                    <label for="zip_file" class="required mb-2">Plugin Zip File</label>
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
                </div>
            </div>
        </form>

        <template #modal_footer>
            <button class="btn btn-success btn-sm" type="button" @click="handleSubmit">
                Submit
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive } from "vue";
import Modal from "../../../components/Modal.vue";
import { FormValidation } from "../../../helpers/Validation";

let plugin_upload_form = ref(null);

const emits = defineEmits(["reload"]);

let fields = reactive({
    zip_file: "",
});

function openModal(user) {
    clearFormData();
    plugin_upload_form.value.open();
}

function clearFormData() {
    formValidation.reset();
    fields.url = "";
}

function setFile() {
}

function handleSubmit() {
    formValidation.validate();
    if (formValidation.isValid()) {
        
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
