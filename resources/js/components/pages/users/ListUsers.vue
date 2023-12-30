<script setup>
    import { onMounted, reactive, ref } from "vue";
    import axios from 'axios';
    const users = ref([]);
    const form = reactive({
        name: '',
        email: '',
        password: ''
    })
    const getUsers = () => {
        axios.get('/api/users')
        .then((res) => {
            users.value = res.data.data
            console.log(res.data.data)
        })
    }

    const createUser = () => {
        axios.post('/api/users', form)
        .then((res) => {
            users.value.unshift(res.data.data)
            form.name = '',
            form.email = '',
            form.password = '',
            $('#createUserModal').modal('hide');
        })
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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
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
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="createUserModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Create User :</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input v-model="form.name" name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input v-model="form.email" name="email" type="email" class="form-control "
                                 id="email" aria-describedby="nameHelp" placeholder="Enter full name" />
                           
                        </div>

                        <div class="form-group">
                            <label for="email">Password</label>
                            <input v-model="form.password" name="password" type="password" class="form-control " id="password" aria-describedby="nameHelp"
                                placeholder="Enter password" />
                            
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="createUser" type="button" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
</template>