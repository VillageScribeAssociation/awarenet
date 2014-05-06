/**
 *  Cells of this type hold a digit of the solution, and have a parent which is a carry and a
 *  parent which is an add or mul (first column only).
 *
 *  @param  id          {String}    ID of a cell
 *  @param  x           {Number}    Location in viewport    
 *  @param  y           {Number}    Location in viewport
 *  @param  parentCarry {String}    ID of carry cell for this column
 *  @param  parentAdd   {String}    ID of parent, carry cells have 0 or 1
 *
 *  Note that that carry of the first column is always 0.
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
