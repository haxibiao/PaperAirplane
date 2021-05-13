import React, { useEffect, useState } from "react";
import { Message, Avatar, Tag, Divider, Placeholder, Loader } from "rsuite";
const { Paragraph } = Placeholder;

import useAxios from "axios-hooks";
import axios from "axios";
import UserStore from "../../../../../store/UserStore";

export default function BotInfoCard(props) {
    const { id, remarks, onGetInfo } = props;

    const [botInfo, setbotInfo] = useState();
    const [{ data: botInfoData, loading, error }, refetch] = useAxios({
        url: "/api/bot/info?id=" + id,
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    useEffect(() => {
        if (botInfoData?.data) {
            // console.log("机器人数据", botInfoData.data);
            setbotInfo(botInfoData?.data);
            onGetInfo(botInfoData?.data);
        }
    }, [botInfoData]);

    if (loading) {
        return (
            <Paragraph style={{ marginTop: 20, marginBottom: 20 }} rows={3}>
                <Loader center content="loading" />
            </Paragraph>
        );
    }

    return botInfo ? (
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
                        <b style={{ paddingLeft: 10 }}>{botInfo?.app_name}</b>

                        {botInfo?.activate_status === 2 ? (
                            <Tag style={{ marginLeft: 10 }} color="green">
                                已激活
                            </Tag>
                        ) : (
                            <Tag color="orange" style={{ marginLeft: 10 }}>
                                未激活
                            </Tag>
                        )}
                    </div>
                    <Divider style={{ background: "#FFF" }} />
                    <div>
                        <p style={{ color: "#0006" }}>{remarks}</p>
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
    );
}
