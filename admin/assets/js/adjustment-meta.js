document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('buddyc_adjustment_create_option');
    if ( ! addButton) {
        return;
    }
    addButton.addEventListener('click', buddycAddNewOptionTable);
});

let optionCount = 6; // Starting count for new options

function buddycAddNewOptionTable() {
    const container = document.querySelector('#buddyc_adjustment-Options_metabox .inside');

    if (!container) {
        console.error('Container div not found');
        return;
    }

    if (optionCount > 10) {
        console.warn('Maximum number of options reached');
        return;
    }

    const newTable = document.createElement('table');
    newTable.className = 'widefat bp-postbox-table';
    newTable.innerHTML = `
        <thead>
            <tr><th colspan="2">Option ${optionCount}</th></tr>
        </thead>
        <tbody>
            <tr>
                <th>Label</th>
                <td>
                    <input type="text" class="buddyc-meta-field" name="option_${optionCount}_label" placeholder="" value="" size="10">
                    <div class="buddyc-meta-description"></div>
                </td>
            </tr>
            <tr>
                <th>Operator</th>
                <td>
                    <select class="buddyc-meta-input buddyc-meta-field" id="option_${optionCount}_operator" name="option_${optionCount}_operator">
                        <option value=""></option>
                        <option value="x">x (multiply)</option>
                        <option value="+">+ (add)</option>
                        <option value="-">- (subtract)</option>
                    </select>
                    <div class="buddyc-meta-description"></div>
                </td>
            </tr>
            <tr>
                <th>Value</th>
                <td>
                    <input type="number" class="buddyc-meta-field" name="option_${optionCount}_value" placeholder="" value="" size="10">
                    <div class="buddyc-meta-description"></div>
                </td>
            </tr>
        </tbody>
    `;

    container.appendChild(newTable);
    optionCount++;
}
