import React, { useState, useEffect, useRef } from "react";
import {
    Table,
    Button,
    Layout,
    MessageBox,
    Message,
    Dialog,
    Form,
    Input,
    Notification,
} from "element-react";
import { Drawer } from "rsuite";

import useAxios from "axios-hooks";
import axios from "axios";
import { observer } from "mobx-react";
import UserStore from "../../../../store/UserStore";
import { CreateApp, ModifyBot } from "./components";

function Index() {
    const [{ data, loading, error }, refetch] = useAxios({
        url: "/api/bot/list",
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    const columns = [
        {
            label: "ID",
            prop: "id",
            width: 80,
        },
        {
            label: "飞书 APP ID",
            prop: "fs_app_id",
            width: 220,
        },
        {
            label: "管理员",
            prop: "user.name",
            width: 180,
        },
        {
            label: "备注",
            prop: "remarks",
        },
        {
            label: "操作",
            fixed: "right",
            width: 180,
            render: (data) => {
                return (
                    <span>
                        <Button
                            type="text"
                            size="small"
                            onClick={() =>
                                showDrawer("创建应用", () => (
                                    <CreateApp
                                        botData={data}
                                        closeDrawer={closeDrawer}
                                    />
                                ))
                            }
                        >
                            创建应用
                        </Button>
                        <Button
                            type="text"
                            size="small"
                            onClick={() =>
                                showDrawer("修改配置", () => (
                                    <ModifyBot botData={data} />
                                ))
                            }
                        >
                            修改配置
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

    const [dialogAddBot, setdialogAddBot] = useState(false);
    const [botConfig, setbotConfig] = useState({});
    const [refCreateBotLoding, setrefCreateBotLoding] = useState(false);
    const ApiCreateBot = (id, secret, remarks) => {
        if (refCreateBotLoding) {
            return;
        }

        setrefCreateBotLoding(true);

        if (!id || !secret) {
            Notification({
                message: "飞书 APP ID 或 APP Secret 未填写！",
            });
            return;
        }

        const data = { app_id: id, app_secret: secret, remarks };

        axios
            .post("/api/bot/create", data, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;

                if (code > 0) {
                    Notification({
                        message: "通知机器人添加成功！",
                        type: "success",
                    });
                    setbotConfig({});
                } else {
                    Notification({
                        message: msg,
                        type: "error",
                    });
                }

                setrefCreateBotLoding(false);
                setdialogAddBot(false);
                refetch();
            })
            .catch((error) => {
                Notification({
                    message: error,
                    type: "error",
                });
                setrefCreateBotLoding(false);
            });
    };

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
        <div className="screen-bots">
            <Layout.Row type="flex" justify="end" style={{ marginBottom: 15 }}>
                <div style={{ flex: 1 }} />
                <div>
                    <Button
                        type="primary"
                        onClick={() => setdialogAddBot(true)}
                    >
                        添加机器人
                    </Button>
                </div>
            </Layout.Row>

            <Table style={{ width: "100%" }} columns={columns} data={list} />

            <Dialog
                title="添加通知机器人"
                visible={dialogAddBot}
                onCancel={() => setdialogAddBot(false)}
            >
                <Dialog.Body>
                    <Form model={botConfig.current}>
                        <Form.Item label="飞书 APP ID" labelWidth="160">
                            <Input
                                value={botConfig.app_id}
                                onChange={(value) => {
                                    setbotConfig({
                                        ...botConfig,
                                        app_id: value,
                                    });
                                }}
                            ></Input>
                        </Form.Item>
                        <Form.Item label="飞书 APP Secret" labelWidth="160">
                            <Input
                                value={botConfig.app_secret}
                                onChange={(value) => {
                                    setbotConfig({
                                        ...botConfig,
                                        app_secret: value,
                                    });
                                }}
                            ></Input>
                        </Form.Item>
                        <Form.Item label="机器人备注" labelWidth="160">
                            <Input
                                value={botConfig.remarks}
                                onChange={(value) => {
                                    setbotConfig({
                                        ...botConfig,
                                        remarks: value,
                                    });
                                }}
                            ></Input>
                        </Form.Item>
                    </Form>
                </Dialog.Body>

                <Dialog.Footer className="dialog-footer">
                    <Button onClick={() => setdialogAddBot(false)}>
                        取 消
                    </Button>
                    <Button
                        type="primary"
                        loading={refCreateBotLoding}
                        onClick={() => {
                            ApiCreateBot(
                                botConfig.app_id,
                                botConfig.app_secret,
                                botConfig.remarks
                            );
                        }}
                    >
                        确 定
                    </Button>
                </Dialog.Footer>
            </Dialog>

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
