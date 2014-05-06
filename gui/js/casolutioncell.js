/**
 *  Cells of this type hold the eigenstate of solution digits
 */

function CASolutionCell(id, x, y) {

    var basicCell = CACell(id, 'solution', x, y, '', '');

    function initialize() {
        basicCell.hypotheses = [];

        var i, j, newHyp, notS;

        for (i = 0, i < 10; i++) {

            newHyp = CAHypothesis(id, i);
            notS = []

            for (j = 0; j < 10; j++) {

                if (i !== j) {
                    notS.push('!' + id + '___' + j);
                }

            }

            newHyp.premises.push(notS.join('.'));
        }

    }

    basicCell.initialize = initialize;

    return basicCell;

}
