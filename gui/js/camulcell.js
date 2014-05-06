/**
 *  Cells of this type descend from solution types and multiply individual solution digits together 
 *
 *  @param  id      {String}    ID of a cell
 *  @param  x       {Number}    Location in viewport    
 *  @param  y       {Number}    Location in viewport
 *  @param  parentA {String}    ID of parent in row A
 *  @param  parentB {String}    ID of parent in row B
 *
 *  Note that that carry of the first column is always 0.
 */

function CAMulCell(id, x, y, parentA, parentB) {

    var basicCell = CACell(id, 'mul', x, y, parentA, parentB);

    //  carry does not begin with any hypothesis, 
    //  gets them from the previous total cell

    function initialize() {
        basicCell.hypotheses = [];
    }

    basicCell.initialize = initialize;

    return basicCell;

}
