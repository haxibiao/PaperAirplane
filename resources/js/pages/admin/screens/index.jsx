/*
 * @Author: Bin
 * @Date: 2021-05-06
 * @FilePath: /PaperAirplane/resources/js/pages/admin/screens/index.jsx
 */

import Home from "./home";
import Apps from "./apps";
import Users from "./users";
import Bots from "./bots";
import Default from "./default";

// 系统状态
const HomeScreen = <Home />;

// 应用管理
const AppsScreen = <Apps />;
// 用户管理
const UsersScreen = <Users />;
// 机器人管理
const BotsScreen = <Bots />;

// 404 页面
const DefaultScreen = <Default />;

export { HomeScreen, AppsScreen, DefaultScreen, UsersScreen, BotsScreen };
