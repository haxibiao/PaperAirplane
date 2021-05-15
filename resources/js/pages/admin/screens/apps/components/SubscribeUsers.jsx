import React, { useEffect, useState } from "react";

import { Table, Button, InputPicker, Icon } from "rsuite";
import { Layout, Notification } from "element-react";

import useAxios from "axios-hooks";
import axios from "axios";
import UserStore from "../../../../../store/UserStore";

const { Column, HeaderCell, Cell, Pagination } = Table;

export default function SubscribeUsers(props) {
    const { data: appData, closeDrawer, refetchApps } = props;

    const [usersData, setusersData] = useState([]);
    const [addUserID, setaddUserID] = useState(null); // 选择添加订阅用户组件值

    // TODO: 这里的数据不是每次打开侧边会触发刷新数据，待优化
    const [{ data: subscribeData, loading, error }, subscribeRefetch] =
        useAxios({
            url: "/api/app/users?id=" + appData.id,
            headers: {
                Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
            },
        });

    useEffect(() => {
        if (subscribeData?.data) {
            // console.log("订阅用户数据", subscribeData.data);
            setusersData(subscribeData?.data);

            if (searchUsers == null) {
                // 触发用户搜索关键词
                apiGetUsers("");
            }
        }

        return () => {
            // 清空选中的添加订阅用户组件的值
            setaddUserID(null);
        };
    }, [subscribeData]);

    const apiUnsubscribe = (app_id, user_id) => {
        if (!app_id || !user_id) {
            Notification({
                message: "应用或用户信息获取异常！",
            });
            return;
        }

        const data = { app_id, user_id };

        axios
            .post("/api/app/user/delete", data, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;

                if (code > 0) {
                    Notification({
                        message: "移除订阅用户成功！",
                        type: "success",
                    });
                } else {
                    Notification({
                        message: msg,
                        type: "error",
                    });
                }

                // 刷新订阅用户列表数据
                subscribeRefetch();

                // 刷新订阅应用列表
                refetchApps();
            })
            .catch((error) => {
                Notification({
                    message: error,
                    type: "error",
                });
            });
    };
    const apiSubscribe = (app_id, user_id) => {
        if (!app_id || user_id == null) {
            Notification({
                message: "应用或用户信息获取异常！",
            });
            return;
        }

        const data = { app_id, user_id };

        axios
            .post("/api/app/user/add", data, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;

                if (code > 0) {
                    Notification({
                        message: "添加订阅用户成功！",
                        type: "success",
                    });
                } else {
                    Notification({
                        message: msg,
                        type: "error",
                    });
                }

                // 刷新订阅用户列表数据
                subscribeRefetch();

                // 清空选中的添加订阅用户组件的值
                setaddUserID(null);

                // 刷新订阅应用列表
                refetchApps();
            })
            .catch((error) => {
                Notification({
                    message: error,
                    type: "error",
                });
            });
    };

    const [searchUsers, setsearchUsers] = useState();
    const apiGetUsers = (key) => {
        axios
            .get("/api/user/search?key=" + key, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg, data } = res?.data;
                let users = [];

                if (code > 0) {
                    users = data?.map((user) => {
                        return {
                            label: user?.fs_user_name,
                            value: user?.id,
                            role: "Master",
                        };
                    });

                    // 刷新订阅用户列表数据
                    setsearchUsers(users);
                }
            })
            .catch((error) => {
                console.log("User gets exception: ", error);
            });
    };

    return (
        <div>
            <p style={{ marginBottom: 20 }}>this is SubscriptionUsers Page…</p>
            <Layout.Row type="flex" justify="end" style={{ marginBottom: 15 }}>
                <div style={{ flex: 1 }} />
                <div>
                    <Button
                        appearance="ghost"
                        style={{ marginRight: 15 }}
                        onClick={subscribeRefetch}
                    >
                        刷新列表
                    </Button>
                </div>
            </Layout.Row>
            <Table
                height={400}
                data={usersData}
                bordered
                onRowClick={(data) => {
                    console.log(data);
                }}
            >
                <Column width={70} align="center">
                    <HeaderCell>ID</HeaderCell>
                    <Cell dataKey="id" />
                </Column>

                <Column width={90}>
                    <HeaderCell>用户名</HeaderCell>
                    <Cell dataKey="name" />
                </Column>

                <Column width={90}>
                    <HeaderCell>姓名</HeaderCell>
                    <Cell dataKey="fs_user_name" />
                </Column>

                <Column width={120}>
                    <HeaderCell>飞书 User ID</HeaderCell>
                    <Cell dataKey="fs_user_id" />
                </Column>

                <Column width={120} fixed="right">
                    <HeaderCell>操作</HeaderCell>

                    <Cell>
                        {(rowData) => {
                            return (
                                <span>
                                    <a
                                        onClick={() =>
                                            apiUnsubscribe(
                                                appData?.id,
                                                rowData?.id
                                            )
                                        }
                                    >
                                        取消订阅
                                    </a>
                                </span>
                            );
                        }}
                    </Cell>
                </Column>
            </Table>

            <Layout.Row type="flex" style={{ marginTop: 15 }}>
                <InputPicker
                    placeholder="请选择一个用户"
                    style={{ flex: 1, marginRight: 15 }}
                    data={searchUsers}
                    value={addUserID}
                    onSearch={apiGetUsers}
                    onChange={(value) => setaddUserID(value)}
                    renderMenu={(menu) => {
                        if (!searchUsers) {
                            return (
                                <p
                                    style={{
                                        padding: 4,
                                        color: "#999",
                                        textAlign: "center",
                                    }}
                                >
                                    <Icon icon="spinner" spin /> Loading...
                                </p>
                            );
                        }
                        return menu;
                    }}
                    block
                />
                <div>
                    <Button
                        appearance="primary"
                        onClick={() => apiSubscribe(appData?.id, addUserID)}
                    >
                        添加用户
                    </Button>
                </div>
            </Layout.Row>
        </div>
    );
}
