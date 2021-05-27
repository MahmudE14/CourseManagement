import React, { useState } from 'react';
import axios from 'axios';

import { BASE_URL } from '../../constants/_meta';

function MainPage(props) {
    const [courses, setCourses] = useState([]);

    if (!courses.length) {
        axios(`${BASE_URL}/courses`).then(({ data }) => setCourses(data));
    }

    return (
        <div className="container-fluid">
            <div className="row">
                { props.children }
            </div>
        </div>
    );
}

export default MainPage;
