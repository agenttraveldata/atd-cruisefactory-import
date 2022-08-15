<p>Enquiry submitted
    for <?php echo $args['summary']->getSpecial() ? $args['summary']->getSpecial()->getName() : $args['summary']->getCruise()->getName(); ?>
    departing <?php echo $args['summary']->getSailingDate()->format( 'd/m/Y' ); ?></p>

<table style="border-spacing: 0;border: none;width: 800px;">
    <tbody>
    <tr>
        <td colspan="2">
            <p>Quote Request for <?php echo $args['first_name']; ?></p>

            <table style="border-spacing: 0;border: none;width: 100%">
                <tbody>
                <tr style="background-color: #c0c0c0;">
                    <td><b>Passenger Details</b></td>
                </tr>
                <tr>
                    <td style="padding: 15px;">
                        First Name: <b><?php echo $args['first_name']; ?></b><br>
                        Last Name: <b><?php echo $args['last_name']; ?></b><br>
                        Email:
                        <b><a href="mailto:<?php echo $args['email_address']; ?>"
                              target="_blank"><?php echo $args['email_address']; ?></a></b><br>
                        Phone Number: <b><?php echo $args['phone_number']; ?></b><br>
                        <p>Passenger Details:<br>
                            <b><?php echo $args['num_adults']; ?> adult(s).</b><br>
                            <b><?php echo $args['num_children'] ?? 0; ?> children.</b>
                        </p>
                    </td>
                </tr>
                <tr style="background-color: #c0c0c0;">
                    <td><b><?php echo $args['summary']->getSpecial() ? 'Special' : 'Cruise'; ?> Details</b></td>
                </tr>
                <tr>
                    <td style="padding: 15px;">
                        Departure Id: <b><?php echo $args['summary']->getId(); ?></b><br>
						<?php echo $args['summary']->getSpecial() ? 'Special' : 'Cruise'; ?> Id:
                        <b><?php echo $args['summary']->getSpecial() ? $args['summary']->getSpecial()->getId() : $args['summary']->getCruise()->getId(); ?></b><br>
                        Cruise Line:
                        <b><?php echo $args['summary']->getCruise()->getCruiseLine()->getName(); ?></b><br>
                        Cruise Destination:
                        <b><?php echo $args['summary']->getCruise()->getDestination()->getName(); ?></b><br>
                        Cruise Length: <b><?php echo $args['summary']->getCruise()->getDuration(); ?> Nights</b><br>
                        Ship Name: <b><?php echo $args['summary']->getCruise()->getShip()->getName(); ?></b><br>
                        Sailing Date:
                        <b><?php echo $args['summary']->getSailingDate()->format( 'd/m/Y' ); ?></b><br>
						<?php if ( $args['summary']->getSpecial() ): ?>
                            Cabin: <strong>
								<?php echo $args['cabin_price'] ? $args['summary']->getSpecial()->getSpecialPrice()->getCabin()->getName() : $args['lead_price']; ?>
                            </strong><br>
                            Pricing: <strong>
								<?php if ( $args['summary']->getSpecialPrice() ): ?>
									<?php echo $args['summary']->getSpecialPrice()->getCurrency()->getSign(); ?><?php echo $args['summary']->getSpecialPrice()->getPrice(); ?>
								<?php elseif ( $args['lead_price'] ): ?>
									<?php echo $args['summary']->getSpecial()->getCurrency()->getSign(); ?><?php echo $args['summary']->getSpecialLeadPrice()->{'getPrice' . $args['lead_price']}(); ?>
								<?php else: ?>
                                    Request Price
								<?php endif; ?>
                            </strong><br>
						<?php elseif ( $args['summary']->getRequestCabin() ): ?>
                            Requested Cabin: <strong>
								<?php echo $args['summary']->getRequestCabin()->getName(); ?>
                            </strong><br>
						<?php else: ?>
                            Cabin: <strong>
								<?php echo $args['cabin_price'] ? $args['summary']->getCruisePrice()->getCabin() : $args['lead_price']; ?>
                            </strong><br>
                            Pricing: <strong>
								<?php if ( $args['pax'] && $args['summary']->getCruisePrice() ): ?>
									<?php echo $args['summary']->getCruisePrice()->getCurrency(); ?><?php echo $args['summary']->getCruisePrice()->{'getPrice' . $args['pax']}(); ?>
								<?php else: ?>
                                    Request Price
								<?php endif; ?>
                            </strong><br>
						<?php endif; ?>
                        Message: <b><?php echo $args['message']; ?></b>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>