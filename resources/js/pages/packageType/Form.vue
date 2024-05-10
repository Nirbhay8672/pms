<template>
    <Modal ref="package_type_form" :id="'package_type_form'">
        <template #modal_title>
            <span>{{ title_text }}</span>
        </template>

        <div class="row gy-2">
            <div class="col-12 mb-2">
                <Field
                    v-model="fields.name"
                    label="Name"
                    label-class="required"
                    type="text"
                    id="name"
                    field="name"
                    placeholder="Enter name"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
        </div>

        <template #modal_footer>
            <button
                class="btn btn-success btn-sm"
                type="button"
                @click="handleSubmit"
            >
                {{ button_text }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive } from "vue";
import Modal from "../../components/Modal.vue";
import { FormValidation } from "../../helpers/Validation";
import { resetObjectKeys } from "../../helpers/utils";
import Field from "../../helpers/Field.vue";
import axios from "axios";
import { packageTypeRoutes } from "../../routes/PackageTypeRoutes";
import { toastAlert } from "../../helpers/alert";

let package_type_form = ref(null);

const emits = defineEmits(["reload"]);

let title_text = ref("");
let button_text = ref("");

let fields = reactive({
    id: "",
    name: "",
});

function openModal(package_type) {
    clearFormData();
    package_type_form.value.open();

    if (package_type) {
        Object.assign(fields, package_type);

        title_text.value = `Update Package Type : ${package_type.name}`;
        button_text.value = "Update";
    } else {
        title_text.value = "Create Package Type";
        button_text.value = "Submit";
    }
}

function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
}

function handleSubmit() {
    formValidation.validate();

    if (formValidation.isValid()) {
        axios
            .post(packageTypeRoutes.createOrUpdate(fields.id ?? null), fields)
            .then((response) => {
                package_type_form.value.close();
                emits('reload');
                toastAlert({ title: response.data.message });
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
        name: {
            required: "Name field is required.",
        },
    })
);
</script>
