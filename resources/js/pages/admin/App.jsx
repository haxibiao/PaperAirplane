import React from "react";
import { AppHeader } from "../../components";

import { Menu } from "element-react";
import {
    useLocation,
    useHistory,
    Link as RouterLink,
    HashRouter,
} from "react-router-dom";

import Routers from "./router";

export default function App() {
    return (
        <HashRouter>
            <div>
                <AppHeader />
                <div className="app-container">
                    <div className="app-menu">
                        <Menu
                            mode="vertical"
                            defaultActive="1"
                            className="el-menu-vertical-demo"
                        >
                            <Menu.ItemGroup title="系统">
                                <Menu.Item index="1">
                                    <i className="el-icon-message"></i>首页
                                </Menu.Item>
                            </Menu.ItemGroup>
                            <Menu.ItemGroup title="管理">
                                <Menu.Item index="2">
                                    <i className="el-icon-message"></i>
                                    机器人管理
                                </Menu.Item>
                                <Menu.Item index="3">
                                    <i className="el-icon-message"></i>应用管理
                                </Menu.Item>
                                <Menu.Item index="4">
                                    <i className="el-icon-message"></i>用户管理
                                </Menu.Item>
                                <Menu.Item index="5">
                                    <i className="el-icon-message"></i>系统配置
                                </Menu.Item>
                            </Menu.ItemGroup>
                        </Menu>
                    </div>
                    <div className="app-content">
                        <Routers />
                    </div>
                </div>
            </div>
        </HashRouter>
    );
}
