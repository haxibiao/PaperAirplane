/*
 * @Author: Bin
 * @Date: 2021-05-06
 * @FilePath: /PaperAirplane/resources/js/pages/admin/router/routes.js
 */
import { HomeScreen, AppsScreen, UsersScreen, DefaultScreen } from "../screens";

const routes = [
    {
        path: "/home",
        exact: true,
        component: HomeScreen,
    },
    {
        path: "/apps",
        exact: true,
        component: AppsScreen,
    },
    {
        path: "/users",
        exact: true,
        component: UsersScreen,
    },
    {
        path: "*",
        component: DefaultScreen,
    },
];

export default routes;
