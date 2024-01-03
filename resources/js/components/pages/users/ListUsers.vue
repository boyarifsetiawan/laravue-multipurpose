<script setup>
    import { onMounted, reactive, ref } from "vue";
    import { Form, Field } from "vee-validate";
    import * as yup from "yup";
    import axios from 'axios';
    
    const users = ref([]);
    const editing = ref(false)
    const formValues = ref();
    const form = ref(null)
    
    const getUsers = () => {
        axios.get('/api/users')
        .then((response) => {
            users.value = response.data.data
        })
    }

    //validation-schema
    const schema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8)
    });

    //show modal for create
    const addUser = () => {
        editing.value = false
        $('#userFormModal').modal('show')
    }
    //create user
    const createUser = (values, { resetForm }) => {
        axios.post('/api/users', values)
                    .then((response) => {
                        users.value.unshift(response.data.data)
                        $('#userFormModal').modal('hide');
                        resetForm()
        })
    }

    //edit User modal
    const editUser = (user) => {
        editing.value = true
        form.value.resetForm();
        $('#userFormModal').modal('show');
        formValues.value = { 
            id: user.id,
            name: user.name,
            email: user.email,
        };
    }

    onMounted(() => {
        getUsers();
    })

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
        <button type="button" class="btn btn-primary" @click="addUser">
            Create User
        </button>
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
                <tbody>
                    <tr v-for="(user, index) in users" :key="user.id">
                        <th scope="row">{{ index+ 1 }}</th>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="userFormModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Create User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="createUser" :validation-schema="schema" v-slot="{errors}" :initial-values="formValues">
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <Field name="name" v-model="formValues.name" type="text" class="form-control" :class="{'is-invalid': errors.name}" id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                                <span class="invalid-feedback">{{ errors.name }}</span>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <Field name="email" type="email" class="form-control" :class="{'is-invalid': errors.email}"
                                    id="email" aria-describedby="nameHelp" placeholder="Enter an Email" />
                                <span class="invalid-feedback">{{ errors.email }}</span>
                            </div>

                            <div class="form-group">
                                <label for="email">Password</label>
                                <Field name="password" type="password" class="form-control" :class="{'is-invalid': errors.password}" id="password" aria-describedby="nameHelp"
                                    placeholder="Enter password" />
                                <span class="invalid-feedback">{{ errors.password }}</span>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>