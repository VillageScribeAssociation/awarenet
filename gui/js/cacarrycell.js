/**
 *  Cells of this type hold the set of possible carries produced by the previous column 
 *
 *  @param  id      {String}    ID of a cell
 *  @param  x       {Number}    Location in viewport    
 *  @param  y       {Number}    Location in viewport
 *  @param  parent  {String}    ID of parent, carry cells have 0 or 1
 *
 *  Note that that carry of the first column is always 0, other carry cells have the total cell of
 *  the previous column as parents
 */

function CACarryCell(id, x, y, parent) {

    var basicCell = CACell(id, 'carry', x, y, '', '');

    //  carry does not begin with any hypothesis, 
    //  gets them from the previous total cell

    function initialize() {
        basicCell.hypotheses = [];

        //  the first carry is always 0
        if (null === parent) {
            var newHyp = CAHypothesis(id, 0);
            newHyp.premises.push('axiom');
            basicCell.hypotheses.push(newHyp);
        }
    }

    basicCell.initialize = initialize;

    return basicCell;

}
