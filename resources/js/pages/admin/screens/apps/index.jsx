import React, { useState, useEffect } from "react";
import { Drawer, Tag } from "rsuite";
import { Table, Button } from "element-react";

import useAxios from "axios-hooks";
import axios from "axios";
import { observer } from "mobx-react";
import UserStore from "../../../../store/UserStore";

import { ViewConfig, ControlApp, SubscribeUsers } from "./components";

function Index() {
    const [{ data, loading, error }, refetch] = useAxios({
        url: "/api/app/list",
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    const columns = [
        {
            label: "ID",
            prop: "id",
            width: 80,
        },
        {
            label: "状态",
            prop: "status",
            width: 80,
            render: (data) => {
                return data.status === 1 ? (
                    <Tag color="green">启用</Tag>
                ) : (
                    <Tag color="red">禁用</Tag>
                );
            },
        },
        {
            label: "管理员",
            prop: "user.name",
            width: 80,
        },
        {
            label: "订阅人数",
            prop: "users.length",
            width: 120,
        },
        {
            label: "应用备注",
            prop: "remarks",
            width: 190,
        },
        {
            label: "绑定通知机器人",
            prop: "bot.remarks",
        },
        {
            label: "操作",
            fixed: "right",
            width: 260,
            render: (data) => {
                return (
                    <span>
                        <Button
                            type="text"
                            size="small"
                            onClick={() =>
                                showDrawer("查看配置", () => (
                                    <ViewConfig
                                        data={data}
                                        closeDrawer={closeDrawer}
                                    />
                                ))
                            }
                        >
                            查看配置
                        </Button>
                        <Button
                            type="text"
                            size="small"
                            onClick={() =>
                                showDrawer("管理应用", () => (
                                    <ControlApp
                                        data={data}
                                        closeDrawer={closeDrawer}
                                    />
                                ))
                            }
                        >
                            管理应用
                        </Button>
                        <Button
                            type="text"
                            size="small"
                            onClick={() =>
                                showDrawer("订阅管理", () => (
                                    <SubscribeUsers
                                        data={data}
                                        closeDrawer={closeDrawer}
                                    />
                                ))
                            }
                        >
                            订阅管理
                        </Button>
                    </span>
                );
            },
        },
    ];

    const [list, setlist] = useState([]);
    useEffect(() => {
        if (data) {
            const new_data = data?.data?.data;
            setlist(new_data);
        }
    }, [data]);

    // 显示侧边栏
    const [isDrawer, setisDrawer] = useState(false);
    const [drawer, setdrawer] = useState(null);
    const showDrawer = (title, view) => {
        setdrawer({
            title,
            view,
        });
        setisDrawer(true);
    };
    const closeDrawer = () => {
        setisDrawer(false);
        setdrawer(null);
    };

    return (
        <div className="screen-apps">
            <Table style={{ width: "100%" }} columns={columns} data={list} />

            <Drawer show={isDrawer} onHide={closeDrawer}>
                <Drawer.Header>
                    <Drawer.Title>{drawer?.title || ""}</Drawer.Title>
                </Drawer.Header>
                <Drawer.Body>
                    {drawer?.view && React.createElement(drawer?.view)}
                </Drawer.Body>
            </Drawer>
        </div>
    );
}

export default observer(Index);
