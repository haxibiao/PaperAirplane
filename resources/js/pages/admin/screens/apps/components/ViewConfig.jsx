/*
 * @Author: Bin
 * @Date: 2021-05-13
 * @FilePath: /PaperAirplane/resources/js/pages/admin/screens/apps/components/ViewConfig.jsx
 */

import React from "react";
import { FlexboxGrid, Tag, Input, InputGroup } from "rsuite";

export default function ViewConfig(props) {
    const { data } = props;
    const webURL = process.env.MIX_APP_URL;

    return (
        <div>
            <FlexboxGrid>
                <FlexboxGrid.Item colspan={6}>
                    <b>应用 ID</b>
                    <p>{data?.id}</p>
                </FlexboxGrid.Item>
                <FlexboxGrid.Item colspan={6}>
                    <b>订阅人数</b>
                    <p>{data?.users.length}</p>
                </FlexboxGrid.Item>
                <FlexboxGrid.Item colspan={6}>
                    <b>管理员</b>
                    <p>{data?.user?.name}</p>
                </FlexboxGrid.Item>
                <FlexboxGrid.Item colspan={6}>
                    <b>状态</b>
                    <div>
                        {data?.status === 1 ? (
                            <Tag color="green">启用</Tag>
                        ) : (
                            <Tag color="red">禁用</Tag>
                        )}
                    </div>
                </FlexboxGrid.Item>
            </FlexboxGrid>
            <FlexboxGrid style={{ marginTop: 30 }}>
                <FlexboxGrid.Item colspan={12}>
                    <b>绑定机器人</b>
                    <p>{data?.bot.remarks}</p>
                </FlexboxGrid.Item>
                <FlexboxGrid.Item colspan={12}>
                    <b>应用备注</b>
                    <p>{data?.remarks}</p>
                </FlexboxGrid.Item>
            </FlexboxGrid>
            <FlexboxGrid style={{ marginTop: 30 }}>
                <FlexboxGrid.Item colspan={22}>
                    <b>API 调用 Sign</b>
                    <InputGroup style={{ marginTop: 10 }}>
                        <Input value={data?.sign} />
                        <InputGroup.Button>重置签名</InputGroup.Button>
                    </InputGroup>
                    <p style={{ marginTop: 5, color: "#0006" }}>
                        用于调用推送通知消息 API
                        时身份认证，如果发生泄漏可以重置签名（之前的签名将会失效）
                    </p>
                </FlexboxGrid.Item>
            </FlexboxGrid>
            <FlexboxGrid style={{ marginTop: 30 }}>
                <FlexboxGrid.Item colspan={22}>
                    <b>邀请订阅链接</b>
                    <InputGroup style={{ marginTop: 10 }}>
                        <Input
                            value={`${webURL}/subscribe/${data?.id || "0"}`}
                        />
                    </InputGroup>
                    <p style={{ marginTop: 5, color: "#0006" }}>
                        用户访问该链接可以主动订阅或取消订阅通知消息。
                    </p>
                </FlexboxGrid.Item>
            </FlexboxGrid>
        </div>
    );
}
