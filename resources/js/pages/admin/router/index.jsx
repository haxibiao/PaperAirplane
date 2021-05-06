/*
 * @Author: Bin
 * @Date: 2021-05-06
 * @FilePath: /PaperAirplane/resources/js/pages/admin/router/index.jsx
 */
import { Switch, Route, Redirect } from "react-router-dom";

import routes from "./routes";

export default function index() {
    return (
        <Switch>
            <Redirect to="/home" from="/" exact />

            {routes.map((item, index) => {
                return item.exact ? (
                    <Route key={index} exact path={item.path}>
                        {item.component}
                    </Route>
                ) : (
                    <Route key={index} path={item.path}>
                        {item.component}
                    </Route>
                );
            })}
        </Switch>
    );
}
