import React, { useEffect, useState } from "react";
import { AppHeader } from "../../components";

import { Menu } from "element-react";
import { useLocation, useHistory } from "react-router-dom";

import Routers from "./router";
import UserStore from "../../store/UserStore";

export default function App() {
    const history = useHistory();
    const location = useLocation();

    // 获取当前登陆用户信息
    useEffect(() => {
        const user =
            document.getElementsByTagName("meta")["user"]?.content || {};
        if (user) {
            const me = JSON.parse(user);
            UserStore.setMe(me);
        }
    }, []);

    // ----- 应用菜单栏配置 -------
    const menus = [
        {
            name: "系统",
            list: [
                {
                    title: "系统状态",
                    path: "/home",
                },
            ],
        },
        {
            name: "管理",
            list: [
                {
                    title: "用户管理",
                    path: "/users",
                },
                {
                    title: "应用管理",
                    path: "/apps",
                },
                {
                    title: "机器人管理",
                    path: "/bots",
                },
                {
                    title: "系统设置",
                    path: "/setting",
                },
            ],
        },
    ];
    const [defaultMenuIndex, setdefaultMenuIndex] = useState(
        menus[0]?.list[0]?.path || "/home"
    );
    const [showMenu, setshowMenu] = useState(false);
    useEffect(() => {
        setshowMenu(false);
        const pathname = location.pathname;
        if (pathname) {
            setshowMenu(false); // 设置为默认不显示菜单栏
            setdefaultMenuIndex(pathname);

            // 循环遍历菜单，当前路由为菜单中路由时显示菜单
            menus.map((item) => {
                const list = item.list;
                list.map((item) => {
                    if (item.path === pathname) {
                        setshowMenu(true); // 设置显示菜单栏
                        return;
                    }
                });
            });
        }
    }, [location]);
    // --------------------------

    return (
        <div>
            <AppHeader />
            <div className="app-container">
                {showMenu ? (
                    <div className="app-menu">
                        <Menu
                            mode="vertical"
                            defaultActive={defaultMenuIndex}
                            className="el-menu-vertical-demo"
                            onSelect={(index) => {
                                if (index === defaultMenuIndex) return;
                                history.replace(index);
                            }}
                        >
                            {menus.map((groupItem, groupIndex) => {
                                return (
                                    <Menu.ItemGroup
                                        key={groupIndex}
                                        title={groupItem?.name}
                                    >
                                        {groupItem?.list.map(
                                            (menuItem, menuIndex) => {
                                                return (
                                                    <Menu.Item
                                                        key={menuIndex}
                                                        index={
                                                            menuItem.path ||
                                                            menuIndex
                                                        }
                                                    >
                                                        <i className="el-icon-message"></i>
                                                        {menuItem?.title}
                                                    </Menu.Item>
                                                );
                                            }
                                        )}
                                    </Menu.ItemGroup>
                                );
                            })}
                        </Menu>
                    </div>
                ) : null}

                <div className="app-content" key={defaultMenuIndex}>
                    <Routers />
                </div>
            </div>
        </div>
    );
}
