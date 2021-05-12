import React, { useState, useEffect } from "react";
import { useHistory } from "react-router";
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

import useAxios from "axios-hooks";
import axios from "axios";
import UserStore from "../../../../../store/UserStore";
import BotInfoCard from "./BotInfoCard";

export default function ModifyBot(props) {
    // botData: 机器人信息
    // closeDrawer: 关闭侧滑弹窗
    // refetchBots: 刷新机器人列表数据
    const { botData, closeDrawer, refetchBots } = props;

    const history = useHistory();

    const [botInfo, setbotInfo] = useState(null);
    const [botDataEdit, setbotDataEdit] = useState({});

    useEffect(() => {
        // console.log("数据", botData);
        setbotDataEdit({ ...botData });
    }, [botData]);

    const [modifyBotLoding, setmodifyBotLoding] = useState(false);
    const ApiModifyBot = (id, fsAppID, fsAppSecret, remarks) => {
        if (modifyBotLoding) return;

        if (!id) {
            Notification({
                message: "机器人信息获取异常！",
            });
            return;
        }

        setmodifyBotLoding(true);

        const data = { id, app_id: fsAppID, app_secret: fsAppSecret, remarks };

        axios
            .post("/api/bot/modify", data, {
                headers: {
                    Authorization: `Bearer ${UserStore.me?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;

                if (code > 0) {
                    Notification({
                        message: "机器人信息修改成功！",
                        type: "success",
                    });
                } else {
                    Notification({
                        message: msg,
                        type: "error",
                    });
                }

                closeDrawer && closeDrawer();
                refetchBots && refetchBots();
                setmodifyBotLoding(false);
            })
            .catch((error) => {
                Notification({
                    message: error,
                    type: "error",
                });
                setmodifyBotLoding(false);
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

            <div>
                <p style={{ marginBottom: 25 }}>ID：{botData?.id}</p>
                <Form
                    onChange={(value) => {
                        // console.log("改变的数据", value);
                        setbotDataEdit(value);
                    }}
                >
                    <FormGroup>
                        <ControlLabel>飞书 APP ID</ControlLabel>
                        <FormControl
                            value={botDataEdit?.fs_app_id}
                            name="id"
                            type="text"
                        />
                    </FormGroup>
                    <FormGroup>
                        <ControlLabel>飞书 APP Secret</ControlLabel>
                        <FormControl
                            value={botDataEdit?.fs_app_secret}
                            name="secret"
                            type="text"
                        />
                    </FormGroup>
                    <FormGroup>
                        <ControlLabel>机器人备注</ControlLabel>
                        <FormControl
                            rows={5}
                            name="remarks"
                            value={botDataEdit?.remarks}
                            componentClass="textarea"
                        />
                    </FormGroup>
                    <FormGroup>
                        <ButtonToolbar>
                            <Button
                                loading={modifyBotLoding}
                                appearance="primary"
                                onClick={() =>
                                    ApiModifyBot(
                                        botData?.id,
                                        botDataEdit?.fs_app_id,
                                        botDataEdit?.fs_app_secret,
                                        botDataEdit?.remarks
                                    )
                                }
                            >
                                更新
                            </Button>
                            <Button appearance="default" onClick={closeDrawer}>
                                取消
                            </Button>
                        </ButtonToolbar>
                    </FormGroup>
                </Form>
            </div>
        </div>
    );
}
