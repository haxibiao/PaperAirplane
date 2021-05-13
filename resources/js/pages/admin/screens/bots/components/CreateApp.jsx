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
import BotInfoCard from "./BotInfoCard";

export default function CreateApp(props) {
    const { botData, closeDrawer } = props;
    // botData: 机器人配置
    // closeDrawer: 关闭弹窗

    const history = useHistory();

    const [botInfo, setbotInfo] = useState(null);
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

        if (!remarks) {
            Notification({
                message: "请填写应用备注，简单描述一下该通知应用的使用场景。",
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
            <BotInfoCard
                id={botData?.id}
                remarks={botData?.remarks}
                onGetInfo={(data) => {
                    setbotInfo(data);
                }}
            />

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
