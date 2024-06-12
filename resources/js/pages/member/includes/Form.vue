<template>
    <Modal ref="member_form" :id="'member_form'" :size="'large'">
        <template #modal_title>
            <span>{{ title_text }}</span>
        </template>

        <form>
            <h6>User Details</h6>
            <hr>
            <div class="row mt-3 gy-2">
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.username"
                        label="Username"
                        label-class="required"
                        type="text"
                        id="username"
                        field="username"
                        placeholder="Enter username"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.email"
                        label="Email"
                        label-class="required"
                        type="text"
                        id="email"
                        field="email"
                        placeholder="Enter email"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.phone_number"
                        label="Phone Number"
                        label-class="required"
                        type="text"
                        id="phone_number"
                        field="phone_number"
                        placeholder="Enter phon number"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
            </div>
            <h6 class="mt-4">Website Details</h6>
            <hr>
            <div class="row mt-3 gy-2">
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.website_name"
                        label="Website Name"
                        label-class="required"
                        type="text"
                        id="website_name"
                        field="website_name"
                        placeholder="Enter website name"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.website_link"
                        label="Website Link"
                        label-class="required"
                        type="text"
                        id="website_link"
                        field="website_link"
                        placeholder="Enter website link"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.wp_username"
                        label="WP Username"
                        type="text"
                        id="wp_username"
                        field="wp_username"
                        placeholder="Enter Wordpress Username"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.wp_password"
                        label="WP Password"
                        type="password"
                        id="wp_password"
                        field="wp_password"
                        placeholder="Enter Wordpress Password"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.otech_username"
                        label="Otech Username"
                        label-class="required"
                        type="text"
                        id="otech_username"
                        field="otech_username"
                        placeholder="Enter Otech Username"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3">
                    <Field
                        v-model="fields.otech_password"
                        label="Otech Password"
                        label-class="required"
                        type="password"
                        id="otech_password"
                        field="otech_password"
                        placeholder="Enter Otech Password"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-3" v-if="fields.id">
                    <Field
                        v-model="fields.licence_key"
                        label="Licence Key"
                        type="text"
                        id="licence_key"
                        field="licence_key"
                        placeholder="Enter Licence Key"
                        disabled
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
            </div>
        </form>

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
import Modal from "../../../components/Modal.vue";
import { FormValidation } from "../../../helpers/Validation";
import { resetObjectKeys } from "../../../helpers/utils";
import Field from "../../../helpers/Field.vue";
import axios from "axios";
import { toastAlert } from "../../../helpers/alert";
import { memberRoutes } from "../../../routes/MemberRoutes";

let member_form = ref(null);
let title_text = ref("");
let button_text = ref("");

const emits = defineEmits(["reload"]);

let fields = reactive({
    id: "",
    username: "",
    email: "",
    phone_number: "",
    website_name: "",
    website_link: "",
    wp_username: "",
    wp_password: "",
    otech_username: "",
    otech_password: "",
    licence_key: "",
});

function openModal(member) {
    clearFormData();
    member_form.value.open();

    title_text.value = member ? `Update Member : ${member.username}` : "Create Member";
    button_text.value = member ? "Update" : "Submit";

    if (member) {
        Object.assign(fields, member);
    }
}

function clearFormData() {
    formValidation.reset();
    title_text.value = "";
    button_text.value = "";
    formValidation.reset();
    resetObjectKeys(fields);
}

function handleSubmit() {
    formValidation.validate();

    if (formValidation.isValid()) {
        axios
            .post(memberRoutes.createOrUpdate(fields.id), fields)
            .then((response) => {
                member_form.value.close();
                emits("reload");
                toastAlert({ title: response.data.message });
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

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        username: {
            required: "Username field is required.",
        },
        email: {
            required: "Email field is required.",
        },
        phone_number: {
            required: "Phone number field is required.",
        },
        website_name: {
            required: "Website name field is required.",
        },
        website_link: {
            required: "Website link field is required.",
        },
        otech_username: {
            required: "Otech username field is required.",
        },
        otech_password: {
            required: "Otech password field is required.",
        },
    })
);
</script>
