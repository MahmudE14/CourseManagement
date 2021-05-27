import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";


import NavBar from "./src/NavBar";
import MainPage from "./src/MainPage";
import Sidebar from "./src/Sidebar";
import AllCourses from "./src/Pages/AllCourses";

function App() {
    return (
        <Router basename="app">
            <NavBar/>
            <MainPage>
                <Sidebar/>
                {/* <AllCourses/> */}
                <Route path="/all-course" component={AllCourses} />
            </MainPage>
        </Router>
    );
}

if (document.getElementById('root')) {
    ReactDOM.render(<App />, document.getElementById('root'));
}
