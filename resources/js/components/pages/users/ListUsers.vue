<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import axios from "axios";
import { useToastr } from "../../../toastr";
import { debounce } from "lodash";

const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
const userIdBeingDeleted = ref(null);

var errors = ref({});
const userForm = reactive({
    id: "",
    name: "",
    email: "",
    password: "",
});

const roles = ref([
    {
        name: 'ADMIN',
        value: 1
    },
    {
        name: 'USER',
        value: 2
    }
])

//show modal confirm delete
const modalConfirmDelete = (user) => {
    $("#modalConfirmDelete").modal("show");
    userIdBeingDeleted.value = user.id;
};

const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`).then(() => {
        toastr.success("User deleted successfully!");
        $("#modalConfirmDelete").modal("hide");
        users.value = users.value.filter(
            (user) => user.id !== userIdBeingDeleted.value
        );
    });
};

//show modal for create
const addUser = () => {
    editing.value = false;
    $("#userFormModal").modal("show");
};
//create user
const createUser = () => {
    axios
        .post("/api/users", userForm)
        .then((res) => {
            // users.value.unshift(res.data.data);
            $("#userFormModal").modal("hide");
            resetForm();
            getUsers();
            toastr.success("User created successfuly!");
        })
        .catch((error) => {
            if (error.response) {
                errors.value = error.response.data.errors;
            }
        });
};
// edit User modal
const editUser = (user) => {
    editing.value = true;
    $("#userFormModal").modal("show");
    userForm.id = user.id;
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.password = user.password;
};
// Update User
const updateUser = () => {
    axios
        .put("/api/users/" + userForm.id, userForm)
        .then((res) => {
            $("#userFormModal").modal("hide");
            resetForm();
            getUsers();
            toastr.success("User updated successfuly!");
        })
        .catch((error) => {
            if (error.response) {
                errors.value = error.response.data.errors;
            }
        });
};

// change role
const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role
    })
    .then(() => {
        toastr.success('Role changed successfully!')
    })
};

// search
const searchQuery = ref(null)

const search = () => {
    axios.get('/api/users/search', {
        params: {
            query: searchQuery.value
        }
    }).then( response => {
        console.log(response)
        users.value = response.data
    }).catch( error => {
        console.log(error)
    })
}

watch(searchQuery, debounce(() => {
    search()
}, 300))

const resetForm = () => {
    errors.value = "";
    userForm.name = null;
    userForm.email = null;
    userForm.password = null;
};

const getUsers = () => {
    axios.get("/api/users").then((response) => {
        users.value = response.data.data;
    });
};

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Button trigger modal -->
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-primary" @click="addUser">
                Create User
            </button>
            <div>
                <input type="text" class="form-control" v-model="searchQuery"/>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Registered Date</th>
                        <th scope="col">Role</th>
                        <th scope="col">Options</th>
                    </tr>
                </thead>
                <tbody v-if="users.length > 0">
                    <tr v-for="(user, index) in users" :key="user.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.created_at }}</td>
                        <td>
                            <select class="form-control text-sm" @change="changeRole(user, $event.target.value)">
                               <option v-for="role in roles" :value="role.value" :selected="user.role == role.name">{{ role.name }}</option>
                            </select>
                        </td>
                        <td>
                            <a href="#" @click.prevent="editUser(user)"
                                ><i class="fa fa-edit"></i
                            ></a>
                            <a
                                href="#"
                                @click.prevent="modalConfirmDelete(user)"
                                ><i class="fa fa-trash text-danger ml-2"></i
                            ></a>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6">
                            No Results..
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div
        class="modal fade"
        id="userFormModal"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Create User</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form
                    @submit.prevent="userForm.id ? updateUser() : createUser()"
                >
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input
                                v-model="userForm.name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors?.name }"
                                id="name"
                                placeholder="Enter full name"
                            />
                            <span
                                class="invalid-feedback"
                                v-if="errors?.name"
                                >{{ errors.name[0] }}</span
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                v-model="userForm.email"
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': errors?.email }"
                                id="email"
                                placeholder="Enter an Email"
                            />
                            <span
                                class="invalid-feedback"
                                v-if="errors?.email"
                                >{{ errors.email[0] }}</span
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <input
                                v-model="userForm.password"
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': errors?.password }"
                                :disabled="editing"
                                id="password"
                                placeholder="Enter password"
                            />
                            <span
                                class="invalid-feedback"
                                v-if="errors?.password"
                                >{{ errors.password[0] }}</span
                            >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            @click="resetForm"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirm delete -->
    <div
        class="modal fade"
        id="modalConfirmDelete"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>Are you sure want to delete this user ?</span>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button
                        @click.prevent="deleteUser"
                        type="button"
                        class="btn btn-primary"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>