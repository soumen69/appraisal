const CrudUtils = {

    loadingRows(columns) {

        let html = '';

        for (let i = 0; i < 8; i++) {

            html += '<tr>';

            for (let j = 0; j < columns; j++) {

                html += `

                    <td>

                        <div class="placeholder-glow">

                            <span class="placeholder col-12"></span>

                        </div>

                    </td>

                `;

            }

            html += '</tr>';

        }

        return html;

    }

};