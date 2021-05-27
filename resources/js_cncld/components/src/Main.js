import React, { useState } from 'react';
import axios from 'axios';

import { BASE_URL } from '../../constants/_meta';
import Course from './AllCourses/Course';

function Main() {
    const [courses, setCourses] = useState([]);

    if (!courses.length) {
        axios(`${BASE_URL}/courses`).then(({ data }) => setCourses(data));
    }

    return (
        <div className="container-fluid">
            <div className="row">

                <main role="main" className="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4"><div className="chartjs-size-monitor" style={{ position: 'absolute', inset: '0px', overflow: 'hidden', pointerEvents: 'none', visibility: 'hidden', zIndex: -1 }}><div className="chartjs-size-monitor-expand" style={{ position: 'absolute', left: 0, top: 0, right: 0, bottom: 0, overflow: 'hidden', pointerEvents: 'none', visibility: 'hidden', zIndex: -1 }}><div style={{ position: 'absolute', width: '1000000px', height: '1000000px', left: 0, top: 0 }} /></div><div className="chartjs-size-monitor-shrink" style={{ position: 'absolute', left: 0, top: 0, right: 0, bottom: 0, overflow: 'hidden', pointerEvents: 'none', visibility: 'hidden', zIndex: -1 }}><div style={{ position: 'absolute', width: '200%', height: '200%', left: 0, top: 0 }} /></div></div>
                    {/* <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                        <h1 className="h2">All Courses</h1>
                        <div className="btn-toolbar mb-2 mb-md-0">
                            <div className="btn-group mr-2">
                                <button className="btn btn-sm btn-outline-secondary">Share</button>
                                <button className="btn btn-sm btn-outline-secondary">Export</button>
                            </div>
                            <button className="btn btn-sm btn-outline-secondary dropdown-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width={24} height={24} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" className="feather feather-calendar"><rect x={3} y={4} width={18} height={18} rx={2} ry={2} /><line x1={16} y1={2} x2={16} y2={6} /><line x1={8} y1={2} x2={8} y2={6} /><line x1={3} y1={10} x2={21} y2={10} /></svg>
                            This week
                        </button>
                        </div>
                    </div> */}
                    {/* <canvas className="my-4 chartjs-render-monitor" id="myChart" width={1479} height={624} style={{ display: 'block', width: '1479px', height: '624px' }} /> */}

                    <h3 className="mt-3 mb-4">All Courses</h3>

                    <div className="table-responsive">
                        <table className="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Thumbnail</th>
                                </tr>
                            </thead>
                            <tbody>
                                {courses.map((val, key) => <Course details={val} id={key+1} key={key} />)}
                            </tbody>
                        </table>
                    </div>
                </main>

            </div>
        </div>
    );
}

export default Main;
