<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="text-2xl font-bold mb-4">Análisis del Problema y Posible Solución</h2>
            
                        <h3 class="text-lg font-semibold mb-2">Descripción del Problema:</h3>
                        <p class="mb-4">La empresa Spira enfrenta el desafío de desarrollar una plataforma que permita gestionar usuarios y cursos de manera eficiente. La necesidad principal radica en la capacidad de crear, editar y eliminar usuarios, así como asignar cursos a dichos usuarios.</p>
                        
                        <h3 class="text-lg font-semibold mb-2">Posible Solución:</h3>
                        <p class="mb-2">Para abordar este problema, se propone desarrollar una aplicación web que cumpla con los siguientes requisitos:</p>
                        
                        <ul class="list-disc list-inside mb-4">
                            <li>Gestión de Usuarios: Implementar funcionalidades para crear, ver, editar y eliminar usuarios. Esto incluye campos como nombre, apellido, correo electrónico, teléfono y rol asignado.</li>
                            <li>Gestión de Cursos: Permitir la creación, visualización, edición y eliminación de cursos. Cada curso debe tener un nombre e intensidad horaria.</li>
                            <li>Asignación de Cursos: Proporcionar una forma sencilla de asignar cursos a usuarios específicos.</li>
                        </ul>

                        <h3 class="text-lg font-semibold mb-2">Tecnologia:</h3>
                        <p class="mb-2">Se construira a traves de framework Laravel para el Backend y para el frontend se usara Vue.js</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
