import React, { useEffect, useState } from "react";

import {
    Form,
    FormGroup,
    ControlLabel,
    FormControl,
    HelpBlock,
    ButtonToolbar,
    Button,
    Message,
    Avatar,
    Row,
    Tag,
    Divider,
} from "rsuite";
import { Notification } from "element-react";

import { useHistory } from "react-router";
import useAxios from "axios-hooks";
import axios from "axios";
import UserStore from "../../../../../store/UserStore";

export default function CreateApp(props) {
    const { botData, closeDrawer } = props;
    // botData: 机器人配置
    // closeDrawer: 关闭弹窗

    const history = useHistory();

    const [botInfo, setbotInfo] = useState();
    const [{ data: botInfoData, loading, error }, refetch] = useAxios({
        url: "/api/bot/info?id=" + botData?.id,
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    useEffect(() => {
        if (botInfoData?.data) {
            // console.log("机器人数据", botInfoData.data);
            setbotInfo(botInfoData?.data);
        }
    }, [botInfoData]);

    const [createAppForm, setcreateAppForm] = useState({});

    const [createAppLoding, setcreateAppLoding] = useState(false);
    const ApiCreateApp = (id, remarks) => {
        if (createAppLoding) return;

        if (!id) {
            Notification({
                message: "机器人信息获取异常！",
            });
            return;
        }

        setcreateAppLoding(true);

        const data = { bot_id: id, remarks };

        axios
            .post("/api/app/create", data, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;

                if (code > 0) {
                    Notification({
                        message: "通知应用创建成功！",
                        type: "success",
                    });
                } else {
                    Notification({
                        message: msg,
                        type: "error",
                    });
                }
                history.push("apps");
                setcreateAppLoding(false);
            })
            .catch((error) => {
                Notification({
                    message: error,
                    type: "error",
                });
                setcreateAppLoding(false);
            });
    };

    return (
        <div>
            {botInfo ? (
                <Message
                    type="info"
                    style={{ marginBottom: 20 }}
                    description={
                        <div>
                            <div
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                }}
                            >
                                <Avatar src={botInfo?.avatar_url} alt="icon" />
                                <b style={{ paddingLeft: 10 }}>
                                    {botInfo?.app_name}
                                </b>

                                {botInfo?.activate_status === 2 ? (
                                    <Tag
                                        style={{ marginLeft: 10 }}
                                        color="green"
                                    >
                                        已激活
                                    </Tag>
                                ) : (
                                    <Tag
                                        color="orange"
                                        style={{ marginLeft: 10 }}
                                    >
                                        未激活
                                    </Tag>
                                )}
                            </div>
                            <Divider style={{ background: "#FFF" }} />
                            <div>
                                <p style={{ color: "#0006" }}>
                                    {botData?.remarks}
                                </p>
                            </div>
                        </div>
                    }
                />
            ) : (
                <Message
                    type="info"
                    style={{ marginBottom: 20 }}
                    description={
                        <p>
                            机器人信息获取失败，请检查是否在飞书中开启该应用的机器人功能：
                            <a href="#">如何开启飞书 APP 的机器人功能？</a>
                        </p>
                    }
                />
            )}

            <Form
                onChange={(value) => {
                    // console.log("改变的数据", value);
                    setcreateAppForm(value);
                }}
            >
                <FormGroup>
                    <ControlLabel>应用备注</ControlLabel>
                    <FormControl
                        rows={5}
                        disabled={!botInfo}
                        name="remarks"
                        componentClass="textarea"
                    />
                </FormGroup>
                <FormGroup>
                    <ButtonToolbar>
                        <Button
                            loading={createAppLoding}
                            appearance="primary"
                            disabled={!botInfo}
                            onClick={() =>
                                ApiCreateApp(botData.id, createAppForm?.remarks)
                            }
                        >
                            创建
                        </Button>
                        <Button appearance="default" onClick={closeDrawer}>
                            取消
                        </Button>
                    </ButtonToolbar>
                </FormGroup>
            </Form>
        </div>
    );
}
