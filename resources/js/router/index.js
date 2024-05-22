import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from '../components/Dashboard.vue';
import Appointments from '../components/pages/appointments/ListAppointments.vue';
import AppointmentForm from '../components/pages/appointments/AppointmentForm.vue';
import Users from '../components/pages/users/ListUsers.vue';
import Settings from '../components/pages/settings/UpdateSetting.vue';
import Profile from '../components/pages/profile/UpdateProfile.vue';
import Login from '../components/pages/auth/Login.vue';

const routes =
    [
        {
            path: '/login',
            name: 'admin.login',
            component: Login
        },
        {
            path: '/admin/dashboard',
            name: 'admin.dashboard',
            component: Dashboard
        },
        {
            path: '/admin/appointments',
            name: 'admin.appointments',
            component: Appointments
        },
        {
            path: '/admin/appointments/create',
            name: 'admin.appointments.create',
            component: AppointmentForm
        },
        {
            path: '/admin/appointments/:id/edit',
            name: 'admin.appointments.edit',
            component: AppointmentForm
        },
        {
            path: '/admin/users',
            name: 'admin.users',
            component: Users
        },
        {
            path: '/admin/settings',
            name: 'admin.settings',
            component: Settings
        },
        {
            path: '/admin/Profile',
            name: 'admin.Profile',
            component: Profile
        },
        {
            path: '/admin/logout',
            name: 'admin.logout',
        },
    ];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;