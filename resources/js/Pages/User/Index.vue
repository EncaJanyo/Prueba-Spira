<script>
import axios from 'axios';
import Swal from 'sweetalert2'
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from "yup";
import DataTable from 'datatables.net-vue3';
import DataTableLib from 'datatables.net-bs5';
import Buttons from 'datatables.net-buttons-bs5'
import 'datatables.net-responsive-bs5';
DataTable.use(DataTableLib);
DataTable.use(Buttons);

const createUserSchema = yup.object({
  name: yup.string().required('El nombre es requerido'),
  last_name: yup.string().required('El apellido es requerido'),
  email: yup.string().email('Debe ingresar un correo valido').required('El correo es requerido'),
  phone: yup.number().typeError('El campo debe ser numerico').positive('El numero debe ser positivo').required('El numero es requerido'),
  password: yup.string().min(8, 'La contraseña debe tener al menos 8 caracteres').required('La contraseña es requerida'),
});

const updateUserSchema = yup.object({
  name: yup.string().required('El nombre es requerido'),
  last_name: yup.string().required('El apellido es requerido'),
  email: yup.string().email('Debe ingresar un correo valido').required('El correo es requerido'),
  phone: yup.number().typeError('El campo debe ser numerico').positive('El numero debe ser positivo').required('El numero es requerido'),
  password: yup.string().nullable(),
});

export default {
    components: {
        DataTable,
        Form,
        Field,
        ErrorMessage,
    },
    data() {
    return {
      loading: false,
      userData: null,
      columns: [
        {data:null, render: function(data,type,row,meta) {return `${meta.row+1}`}},
        {data:'name'},
        {data:'last_name'},
        {data:'email'},
        {data:'phone'},
        {
            data: 'roles',
            render: function(data, type, row) {
                if (row.roles && row.roles[0].name) {
                    return row.roles[0].name;
                } else {
                    return 'Sin rol';
                }
            }
        },
        {
            data: null,
            render: (data, type, row) => {
                const editButton = document.createElement('button');
                editButton.className = 'btn btn-success';
                editButton.textContent = 'Editar';
                editButton.addEventListener('click', () => {
                    this.openCreateModal(row);
                });

                const deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-danger';
                deleteButton.textContent = 'Eliminar';
                deleteButton.addEventListener('click', () => {
                    this.deleteUser(row.id);
                });

                const container = document.createElement('div');
                container.appendChild(editButton);
                container.appendChild(deleteButton);

                return container;
            }
        },
      ],
      error: null,
      showCreateModal: false,
      newUserName: '',
      newUserLastName: '',
      newUserEmail: '',
      newUserPhone: '',
      newUserPassword: null,
      editingUser: null,
      editMode: false,
      roles: [],
      selectedRole: null,
    };
  },
  mounted() {
    this.fetchUserData();
    this.fetchRoles();
  },
  methods: {
    async fetchUserData() {
      this.loading = true;
      try {
        const token = this.$page.props.token;
        const response = await axios.get('/api/users', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
                });
        this.userData = response.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchRoles() {
        try {
            const token = this.$page.props.token;
            const response = await axios.get('/api/roles', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            this.roles = response.data;
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    },
    openCreateModal(user) {
        if (user) {
            this.editingUser = user;
            this.newUserName = user.name;
            this.newUserLastName = user.last_name;
            this.newUserEmail = user.email;
            this.newUserPhone = user.phone;
            this.newUserPassword = '',
            this.selectedRole = user.roles[0].name;
            this.editMode = true;
        } else {
            this.editingUser = null;
            this.newUserName = '';
            this.newUserLastName = '';
            this.newUserEmail = '';
            this.newUserPhone = '';
            this.newUserPassword = null,
            this.selectedRole = 'student';
            this.editMode = false;
        }
        this.showCreateModal = true;
    },
    closeCreateModal() {
      this.showCreateModal = false;
      this.newUserName = '';
    },
    async createUser() {
        try {
            const token = this.$page.props.token;
            const response = await axios.post('/api/users', {
                name: this.newUserName,
                last_name: this.newUserLastName,
                email: this.newUserEmail,
                phone: this.newUserPhone,
                password: this.newUserPassword,
                password_confirmation: this.newUserPassword,
                role: this.selectedRole,
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            await this.fetchUserData();
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El usuario se ha creado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al crear el usuario!'
            });
        } finally {
            this.loading = false;
        }
      this.closeCreateModal();
    },
    async updateUser() {
        try {
        const token = this.$page.props.token;
        const response = await axios.put(`/api/users/${this.editingUser.id}`, {
            id: this.editingUser.id,
            name: this.newUserName,
            last_name: this.newUserLastName,
            email: this.newUserEmail,
            phone: this.newUserPhone,
            password: this.newUserPassword,
            password_confirmation: this.newUserPassword,
            role: this.selectedRole,
        }, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        await this.fetchUserData();
        Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El usuario se ha actualizado exitosamente!'
            });
      } catch (error) {
        Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al actualizar el usuario!'
            });
      } finally {
        this.loading = false;
      }
      this.closeCreateModal();
    },
    async deleteUser(id) {
        const confirmDelete = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'Una vez eliminado, no podrás recuperar este usuario.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        });
        if (confirmDelete.isConfirmed) {
        try {
          const token = this.$page.props.token;
          await axios.delete(`/api/users/${id}`, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          await this.fetchUserData();
          Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El usuario se ha eliminado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al eliminar el usuario!'
            });
        }
      }
    }
  },
  computed: {
    validationSchema() {
      return this.editMode ? updateUserSchema : createUserSchema;
    }
  }
};
</script>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>


<template>
    <AppLayout title="User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <button class="btn btn-primary m-2" @click="openCreateModal(null)">Crear</button>

                    <div class="modal-overlay" v-if="showCreateModal">
                        <div class="modal">
                            <div class="modal-content">
                            <Form :validation-schema="validationSchema" @submit="editMode ? updateUser() : createUser()">
                                <h1 v-if="editMode">Editar Usuario</h1>
                                <h1 v-else>Crear Nuevo Usuario</h1>
                                <div class="form-group">
                                <label for="userName">Nombre del usuario:</label>
                                <Field v-model="newUserName" id="userName" name="name" placeholder="Ingrese el nombre del usuario" />
                                <ErrorMessage name="name" />
                                </div>
                                <div class="form-group">
                                <label for="userLastName">Apellido del usuario:</label>
                                <Field v-model="newUserLastName" id="userLastName" name="last_name" placeholder="Ingrese el apellido del usuario" />
                                <ErrorMessage name="last_name" />
                                </div>
                                <div class="form-group">
                                <label for="userEmail">Correo:</label>
                                <Field v-model="newUserEmail" id="userEmail" name="email" placeholder="Ingrese el correo del usuario" />
                                <ErrorMessage name="email" />
                                </div>
                                <div class="form-group">
                                <label for="userPhone">Telefono:</label>
                                <Field v-model="newUserPhone" id="userPhone" name="phone" placeholder="Ingrese el telefono del usuario"/>
                                <ErrorMessage name="phone" />
                                </div>
                                <div class="form-group">
                                <label for="userPassword">Contraseña:</label>
                                <Field v-model="newUserPassword" id="userPassword" name="password" placeholder="Ingrese la contraseña para el usuario"/>
                                <ErrorMessage name="password" />
                                </div>
                                <div class="form-group">
                                    <label for="userRole">Rol:</label>
                                    <select v-model="selectedRole" id="userRole" name="role" class="form-select">
                                        <option v-for="role in roles" :value="role.name" :key="role.id">{{ role.name }}</option>
                                    </select>
                                </div>
                                <div class="button-group">
                                <button class="btn btn-primary">
                                    {{ editMode ? 'Guardar' : 'Crear' }}
                                </button>
                                <button class="btn btn-secondary" @click="closeCreateModal">Cerrar</button>
                                </div>
                            </Form>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="table-responsive m-3">
                                <DataTable :data="userData" :columns="columns" class="table table-striped table-bordered display" :options="{responsive:true,autoWidth:false,dom:'Bfrtip'}">
                                        <thead class="border-b">
                                            <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Correo</th>
                                            <th>Telefono</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                            </tr>
                                        </thead>
                                </DataTable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<style>
@import '../../../../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css';
@import '../../../css/Datatable.css';
@import '../../../css/Style.css';
</style>
