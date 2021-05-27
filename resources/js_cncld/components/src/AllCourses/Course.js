import React from 'react';

export default function Course(props) {
    let course = props.details;

    return (
        <tr>
            <td>{ props.id }</td>
            <td>{ course.title }</td>
            <td>{ course.code }</td>
            <td>{ course.description }</td>
            <td>
                <img src={ course.thumbnail } width={80} />
            </td>
            {/* <td>{ course.thumbnail }</td> */}
        </tr>
    );
}
