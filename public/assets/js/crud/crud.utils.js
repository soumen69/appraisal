const CrudUtils = {

    loadingRows(columns) {

        let html = '';

        for (
            let i = 0;
            i < 8;
            i++
        ) {

            html += '<tr>';

            for (
                let j = 0;
                j < columns;
                j++
            ) {

                html += `
                    <td>
                        <div class="placeholder-glow">

                            <span
                                class="placeholder col-12">
                            </span>

                        </div>
                    </td>
                `;
            }

            html += '</tr>';
        }

        return html;
    },


    escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

};