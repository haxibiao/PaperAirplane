import React, { useEffect, useState } from "react";

import { Table } from "rsuite";

import useAxios from "axios-hooks";
import axios from "axios";
import UserStore from "../../../../../store/UserStore";

const { Column, HeaderCell, Cell, Pagination } = Table;

export default function SubscribeUsers(props) {
    const { data: appData, closeDrawer } = props;

    const [usersData, setusersData] = useState([]);
    const [{ data: subscribeData, loading, error }, refetch] = useAxios({
        url: "/api/app/users?id=" + appData.id,
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    useEffect(() => {
        if (subscribeData?.data) {
            console.log("订阅用户数据", subscribeData.data);
            setusersData(subscribeData?.data);
        }
    }, [subscribeData]);

    return (
        <div>
            <p style={{ marginBottom: 20 }}>this is SubscriptionUsers Page…</p>
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
                            function handleAction() {
                                alert(`id:${rowData.id}`);
                            }
                            return (
                                <span>
                                    <a onClick={handleAction}> 取消订阅 </a>
                                </span>
                            );
                        }}
                    </Cell>
                </Column>
            </Table>
        </div>
    );
}
