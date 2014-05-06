/**
 *  Cells of this type add together the output of the total states of two other cells - they
 *  have mul or other add cells as parents. 
 *
 *  @param  id      {String}    ID of a cell
 *  @param  x       {Number}    Location in viewport    
 *  @param  y       {Number}    Location in viewport
 *  @param  parent1 {String}    ID of first parent (top)
 *  @param  parent2 {String}    ID of second parent (bottom)
 *
 *  Note that that carry of the first column is always 0.
 */

function CAAddCell(id, x, y, parent1, parent2) {

    var basicCell = CACell(id, 'add', x, y, parent1, parent2);

    //  initial hypotheses will be built when first evaluated

    function initialize() {
        basicCell.hypotheses = [];
    }

    basicCell.initialize = initialize;

    return basicCell;

}
