<script>
import axios from 'axios';
import Swal from 'sweetalert2'
import DataTable from 'datatables.net-vue3';
import DataTableLib from 'datatables.net-bs5';
import Buttons from 'datatables.net-buttons-bs5'
import 'datatables.net-responsive-bs5';
DataTable.use(DataTableLib);
DataTable.use(Buttons);

export default {
    components: {
        DataTable,
    },
    data() {
    return {
      loading: false,
      userData: null,
      courses_assigned: null,
      courses_unassigned: null,
      columns: [
        {data:null, render: function(data,type,row,meta) {return `${meta.row+1}`}},
        {data:'name'},
        {data:'last_name'},
        {
            data: null,
            render: (data, type, row) => {
                const editButton = document.createElement('button');
                editButton.className = 'btn btn-success';
                editButton.textContent = 'Ver';
                editButton.addEventListener('click', () => {
                    this.openCreateModal(row);
                });

                return editButton;
            }
        },
      ],
      error: null,
      showCreateModal: false,
      selectedUser: null,
      UserName: null,
      UserLast: null,
      showDeleteButton: false,
      selectedUserHasCourses: false,
    };
  },
  mounted() {
    this.fetchUserData();
  },
  methods: {
    async fetchUserData() {
      this.loading = true;
      try {
        const token = this.$page.props.token;
        const response = await axios.get('/api/assigns', {
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
    async fetchAssign(id) {
        try {
            const token = this.$page.props.token;
            const response = await axios.get(`/api/users/${id}/assigned-courses`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            this.courses_assigned = response.data;
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    },
    async fetchUnassign(id) {
        try {
            const token = this.$page.props.token;
            const response = await axios.get(`/api/users/${id}/unassigned-courses`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            this.courses_unassigned = response.data;
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    },
    async openCreateModal(user) {
        this.selectedUser = user;
        this.UserName = user.name;
        this.UserLast = user.last_name;
        await this.fetchAssign(user.id);
        await this.fetchUnassign(user.id);
        this.selectedUserHasCourses = this.courses_assigned.length > 0;
        this.showDeleteButton = this.selectedUserHasCourses;
        this.showCreateModal = true;
    },
    closeCreateModal() {
      this.showCreateModal = false;
    },
    moveCourse(course, direction) {
        if (direction === 'assigned') {
            this.courses_assigned.push(course);
            this.courses_unassigned = this.courses_unassigned.filter(item => item.id !== course.id);
        } else if (direction === 'unassigned') {
            this.courses_unassigned.push(course);
            this.courses_assigned = this.courses_assigned.filter(item => item.id !== course.id);
        }
    },
    async assignCourses() {
        if (this.selectedUserHasCourses) {
            await this.updateAssign();
        } else {
            await this.createAssign();
        }
    },
    async createAssign() {
        try {
            const token = this.$page.props.token;
            const response = await axios.post('/api/assigns', {
                user_id: this.selectedUser.id,
                course_ids: this.courses_assigned.map(course => course.id)
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡Los cursos se han asignado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al asignar los cursos!'
            });
        } finally {
            this.loading = false;
        }
        this.closeCreateModal();
    },
    async updateAssign() {
        try {
            const token = this.$page.props.token;
            const response = await axios.put(`/api/assigns/${this.selectedUser.id}`, {
                course_ids: this.courses_assigned.map(course => course.id)
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡Los cursos se han actualizado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al actualizar los cursos!'
            });
        } finally {
            this.loading = false;
        }
        this.closeCreateModal();
    },
    async deleteAssign(userId) {
        const confirmDelete = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'Una vez eliminadas, no podrás recuperar estas asignaciones.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        if (confirmDelete.isConfirmed) {
            try {
                const token = this.$page.props.token;
                await axios.delete(`/api/assigns/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '¡Las asignaciones se han eliminado exitosamente!'
                });
                this.closeCreateModal();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '¡Ha ocurrido un error al eliminar las asignaciones!'
                });
            }
        }
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
    <AppLayout title="Assign">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Asignar Cursos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">.

                    <div class="modal-overlay" v-if="showCreateModal">
                        <div class="modal">
                            <div class="modal-content">
                                <h1>Asignar Cursos</h1>
                                <p>Usuario: {{ selectedUser.name }} {{ selectedUser.last_name }}</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h2>Asignación de Cursos</h2>
                                        <div class="course-table-container">
                                            <table class="course-table">
                                                <thead>
                                                    <tr>
                                                        <th>Cursos No Asignados</th>
                                                        <th>Cursos Asignados</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="scrollable-cell">
                                                            <ul class="list-unstyled">
                                                                <li v-for="course in courses_unassigned" :key="course.id" @click="moveCourse(course, 'assigned')">{{ course.name }}</li>
                                                            </ul>
                                                        </td>
                                                        <td class="scrollable-cell">
                                                            <ul class="list-unstyled">
                                                                <li v-for="course in courses_assigned" :key="course.id" @click="moveCourse(course, 'unassigned')">{{ course.name }}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="button-group">
                                    <button class="btn btn-primary" @click="assignCourses">
                                        {{ selectedUserHasCourses ? 'Actualizar' : 'Asignar' }}
                                    </button>
                                    <button v-if="showDeleteButton" class="btn btn-secondary btn-danger-1" @click="deleteAssign(selectedUser.id)">
                                        Eliminar Asignaciones
                                    </button>
                                    <button class="btn btn-secondary" @click="closeCreateModal">Cerrar</button>
                                </div>
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
.course-table {
        border-collapse: collapse;
        width: 100%;
    }

    .course-table th, .course-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .course-table th {
        background-color: #f2f2f2;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .course-table td {
        position: sticky;
        top: 0;
    }

    .scrollable-cell {
        max-height: 150px;
        overflow-y: auto;
    }

    .scrollable-cell ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }
</style>

