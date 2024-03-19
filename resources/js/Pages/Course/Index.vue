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

const schema = yup.object({
  name: yup.string().required('El nombre es requerido'),
  hour: yup.number().typeError('El campo debe ser numerico').positive('El numero debe ser positivo').required('La intensidad horaria es requerida'),
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
      courseData: null,
      columns: [
        {data:null, render: function(data,type,row,meta) {return `${meta.row+1}`}},
        {data:'name'},
        {data:'hour'},
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
                    this.deleteCourse(row.id);
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
      newCourseName: '',
      newCourseHours: '',
      editingCourse: null,
      editMode: false
    };
  },
  mounted() {
    this.fetchCourseData();
  },
  methods: {
    async fetchCourseData() {
      this.loading = true;
      try {
        const token = this.$page.props.token;
        const response = await axios.get('/api/courses', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
                });
        this.courseData = response.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    openCreateModal(course) {
        if (course) {
            this.editingCourse = course;
            this.newCourseName = course.name;
            this.newCourseHours = course.hour;
            this.editMode = true;
        } else {
            this.editingCourse = null;
            this.newCourseName = '';
            this.newCourseHours = '';
            this.editMode = false;
        }
        this.showCreateModal = true;
    },
    closeCreateModal() {
      this.showCreateModal = false;
      this.newCourseName = '';
    },
    async createCourse() {
        try {
            const token = this.$page.props.token;
            const response = await axios.post('/api/courses', {
                name: this.newCourseName,
                hour: this.newCourseHours
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            await this.fetchCourseData();
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El curso se ha creado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al crear el curso!'
            });
        } finally {
            this.loading = false;
        }
        this.newCourseName = '';
        this.newCourseHours = '';
      this.closeCreateModal();
    },
    async updateCourse() {
        try {
        const token = this.$page.props.token;
        const response = await axios.put(`/api/courses/${this.editingCourse.id}`, {
          name: this.newCourseName,
          hour: this.newCourseHours
        }, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        await this.fetchCourseData();
        Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El curso se ha actualizado exitosamente!'
            });
      } catch (error) {
        Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al actualizar el curso!'
            });
      } finally {
        this.loading = false;
      }
      this.closeCreateModal();
    },
    async deleteCourse(id) {
        const confirmDelete = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'Una vez eliminado, no podrás recuperar este curso.',
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
          await axios.delete(`/api/courses/${id}`, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          await this.fetchCourseData();
          Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '¡El curso se ha eliminado exitosamente!'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '¡Ha ocurrido un error al eliminar el curso!'
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
    <AppLayout title="Course">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cursos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <button class="btn btn-primary m-2" @click="openCreateModal(null)">Crear</button>

                    <div class="modal-overlay" v-if="showCreateModal">
                        <div class="modal">
                            <div class="modal-content">
                            <Form :validation-schema="schema" @submit="editMode ? updateCourse() : createCourse()">
                                <h1 v-if="editMode">Editar Curso</h1>
                                <h1 v-else>Crear Nuevo Curso</h1>
                                <div class="form-group">
                                <label for="courseName">Nombre del Curso:</label>
                                <Field v-model="newCourseName" id="courseName" name="name" placeholder="Ingrese el nombre del curso" />
                                <ErrorMessage name="name" />
                                </div>
                                <div class="form-group">
                                <label for="courseHours">Intensidad Horaria:</label>
                                <Field v-model="newCourseHours" id="courseHours" name="hour" placeholder="Ingrese la intensidad horaria"/>
                                <ErrorMessage name="hour" />
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
                                <DataTable :data="courseData" :columns="columns" class="table table-striped table-bordered display" :options="{responsive:true,autoWidth:false,dom:'Bfrtip'}">
                                        <thead class="border-b">
                                            <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Intensidad horaria</th>
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
