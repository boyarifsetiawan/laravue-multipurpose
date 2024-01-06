<script setup>
    import { onMounted, reactive, ref } from "vue";
    import axios from 'axios';
    
    const users = ref({});
    const editing = ref(false)

    var errors = ref({});
    const userForm = reactive({
        name: '',
        email: '',
        password: ''
    })
    
    const getUsers = () => {
        axios.get('/api/users')
        .then((response) => {
            users.value = response.data.data
        })
    }

    //show modal for create
    const addUser = () => {
        editing.value = false
        $('#userFormModal').modal('show')
    }
    //create user
    const createUser = () => { axios.post('/api/users',userForm)
        .then( (res) => {
        $('#userFormModal').modal('hide')
          userForm.name =''
          userForm.email =''
          userForm.password =''
          errors.value=''
          getUsers()
        }).catch((error) => {
            if (error.response) {
                errors.value = error.response.data.errors
            }
        })
    }
    // edit User modal
    const editUser = (user) => {
    console.log(user)
        editing.value = true;
        $('#userFormModal').modal('show');
        userForm.name = user.name
        userForm.email = user.email
        userForm.password = user.password
    }

    const UpdateUser = (id) => {
        axios.put('/api/users/${id}/update', userForm)
            .then((res) => {
                console.log(res)
            })
    }
    const resetForm = () => {
        errors.value = ''
        userForm.name = null
        userForm.email = null
        userForm.password = null
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
                <form @submit.prevent="createUser">
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input v-model="userForm.name" type="text" class="form-control" :class="{'is-invalid': errors?.name}" id="name" placeholder="Enter full name" />
                                <span class="invalid-feedback" v-if="errors?.name">{{ errors.name[0] }}</span>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input v-model="userForm.email" type="email" class="form-control" :class="{'is-invalid': errors?.email}"
                                    id="email" placeholder="Enter an Email" />
                                <span class="invalid-feedback" v-if="errors?.email">{{ errors.email[0] }}</span>
                            </div>

                            <div class="form-group">
                                <label for="email">Password</label>
                                <input v-model="userForm.password" type="password" class="form-control" :class="{'is-invalid': errors?.password}" 
                                :disabled="editing" id="password" placeholder="Enter password" />
                                <span class="invalid-feedback" v-if="errors?.password">{{ errors.password[0] }}</span>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" @click="resetForm" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>