import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Main from "./components/Main.vue";
import Error404 from "./components/Error404.vue";

const routes = [
  {
    path: "/",
    name: "Main",
    component: Main,
  },
  {
    path: "/:pathMatch(.*)*",
    name: "Error404",
    component: Error404,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
