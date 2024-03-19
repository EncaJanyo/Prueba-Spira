<script>
import axios from 'axios';
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
      listData: null,
      columns: [
        {data:null, render: function(data,type,row,meta) {return `${meta.row+1}`}},
        {data:'name'},
        {data:'last_name'},
        {data:'course_name'},
      ],
      error: null,
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
        const response = await axios.get('/api/lists', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
                });
        const transformedData = this.transformData(response.data);
        this.listData = transformedData;
        console.log(this.listData);
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    transformData(data) {
        const transformedData = [];
        data.forEach(user => {
            user.courses.forEach(course => {
                transformedData.push({
                    id: user.id,
                    name: user.name,
                    last_name: user.last_name,
                    course_name: course.name
                });
            });
        });
        return transformedData;
    },
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
                Listado de usuarios con sus respectivos cursos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="table-responsive m-3">
                                <DataTable :data="listData" :columns="columns" class="table table-striped table-bordered display" :options="{responsive:true,autoWidth:false,dom:'Bfrtip'}">
                                        <thead class="border-b">
                                            <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Curso</th>
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
</style>
