import React, { useEffect, useState } from "react";

import { Avatar, Icon, Divider, Message, Button } from "rsuite";

import useAxios from "axios-hooks";
import axios from "axios";

import LoadView from "./Load";
import ErrorView from "./Error";

export default function App(props) {
    const { user, appID } = props;

    const [appData, setappData] = useState(null);
    const [errorMsg, seterrorMsg] = useState("");

    const [
        { data: appDataCallBack, loading: appLoading, error: appError },
        appRefetch,
    ] = useAxios({
        url: "/api/use/app/" + appID,
        headers: {
            Authorization: `Bearer ${user?.api_token || ""}`,
        },
    });

    useEffect(() => {
        if (appDataCallBack?.data) {
            setappData(appDataCallBack?.data);
        } else if (appDataCallBack) {
            const { code, msg } = appDataCallBack;
            seterrorMsg(msg);
        }
    }, [appDataCallBack]);

    // 取消订阅请求
    const [unsubscribeLoading, setunsubscribeLoading] = useState(false);
    const apiUnsubscribe = () => {
        if (unsubscribeLoading) {
            return;
        }
        setunsubscribeLoading(true);
        axios
            .post("/api/use/app/" + appID + "/unsubscribe", null, {
                headers: {
                    Authorization: `Bearer ${user?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;
                if (code > 0) {
                    appRefetch();
                }
                setunsubscribeLoading(false);
            })
            .catch((error) => {
                setunsubscribeLoading(false);
            });
        setunsubscribeLoading(false);
    };

    // 订阅消息请求
    const [subscribeLoading, setsubscribeLoading] = useState(false);
    const apiSubscribe = () => {
        if (subscribeLoading) {
            return;
        }
        setsubscribeLoading(true);

        axios
            .post("/api/use/app/" + appID + "/subscribe", null, {
                headers: {
                    Authorization: `Bearer ${user?.api_token || ""}`,
                },
            })
            .then((res) => {
                const { code, msg } = res?.data;
                if (code > 0) {
                    appRefetch();
                }
                setsubscribeLoading(false);
            })
            .catch((error) => {
                setsubscribeLoading(false);
            });
        setsubscribeLoading(false);
    };

    // 加载页面 and 加载失败页面渲染
    if (appLoading) {
        return <LoadView />;
    }

    if (appError || errorMsg) {
        return <ErrorView message={appError?.message || errorMsg} />;
    }

    return (
        <div className="app-content">
            <div className="view-head">
                <Avatar size="lg" src={appData?.bot?.icon} />
                <div className="icon">
                    <Icon className="ic-link" icon="link" size="lg" />
                </div>
                <Avatar circle size="lg" src={user?.fs_user_avatar_url} />
            </div>
            <div className="view-msg">
                <b className="text">
                    订阅【 {appData?.bot?.name || "未知应用"}{" "}
                    】相关的消息推送通知
                </b>
            </div>
            <Divider />
            <div className="view-info">
                <p className="text">你的下列飞书账号将会收到消息：</p>
                <div className="user">
                    <Avatar src={user?.fs_user_avatar_url} />
                    <div>
                        <p className="name">{user?.fs_user_name}</p>
                        <p className="desc">飞书账号</p>
                    </div>
                </div>

                <p className="text" style={{ marginTop: 35 }}>
                    此通知应用主要提供下列相关消息：
                </p>
                <Message
                    type="info"
                    description={appData?.remarks}
                    style={{ marginTop: 10 }}
                />
            </div>
            <div className="view-bar">
                <Button
                    loading={unsubscribeLoading || subscribeLoading}
                    onClick={
                        appData?.is_subscribe ? apiUnsubscribe : apiSubscribe
                    }
                    color={appData?.is_subscribe ? "red" : "blue"}
                    style={{ paddingTop: 15, paddingBottom: 15 }}
                    appearance="primary"
                    block
                >
                    {appData?.is_subscribe ? "取消订阅" : "订阅通知"}
                </Button>
            </div>
        </div>
    );
}
