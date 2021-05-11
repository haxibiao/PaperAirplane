import React, { useState, useEffect } from "react";
import { Table, Button } from "element-react";

import useAxios from "axios-hooks";
import axios from "axios";
import { observer } from "mobx-react";
import UserStore from "../../../../store/UserStore";

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
            render: () => {
                return (
                    <span>
                        <Button type="text" size="small">
                            查看配置
                        </Button>
                        <Button type="text" size="small">
                            管理应用
                        </Button>
                        <Button type="text" size="small">
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

    return (
        <div className="screen-apps">
            <Table
                style={{ width: "100%" }}
                columns={columns}
                data={list}
            />
        </div>
    );
}

export default observer(Index);
