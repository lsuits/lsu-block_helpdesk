<?php

require_once $CFG->libdir . '/formslib.php';

class helpdesk_searchform extends moodleform {
    function definition() {
        $m =& $this->_form;

        foreach ($this->_customdata['criterion'] as $k => $c) {
            $options = $this->_customdata['availability'];
            $elements = array(
                $m->createElement('select', "{$k}_equality", '', $options),
                $m->createElement('text', "{$k}_terms", '', array('size' => 60))
            );
            $m->setType("{$k}_terms",PARAM_TEXT);

            $m->addGroup($elements, $k, $c, array(' '), false);
            $m->setDefault("{$k}_equality", 'contains');
        }

        $m->addElement('hidden', 'mode', $this->_customdata['mode']);
        $m->setType('mode',PARAM_ALPHA);

        $buttons = array(
            $m->createElement('submit', 'submit', get_string('submit')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'action_buttons', "&nbsp;", array(' '), false);
    }
}
