<tr valign="top">
	<th scope="row" class="titledesc"> 
		<?php echo wp_kses_post( $data['title'] ); ?> 
	</th>
	<td class="forminp">
		<table class="wc_shipping widefat wp-list-table" cellspacing="0">
			<thead>
				<tr>
					<th>
						Service Name
					</th>
					<th>
						Additional Fee
					</th>
					<th style="width:14%;text-align:center;">
						Active
					</th>
				</tr>
			</thead>
			<tbody>
				<?php $idx = 0; ?>
				<?php foreach ($services as $service): ?>
					<tr class="service">
						<td>
							<?php echo $service['name'] ?>
							<input type="hidden" value="<?php echo $service['id'] ?>" name="<?php echo esc_attr( $field_key ) . '_' . $idx . '_id'; ?>" >
							<input type="hidden" value="<?php echo $service['name'] ?>" name="<?php echo esc_attr( $field_key ) . '_' . $idx . '_name';  ?>" >
						</td>
						<td>
							<input type="number" value="<?php echo $service['add_fee'] ?>" name="<?php echo esc_attr( $field_key ) . '_' . $idx . '_add_fee';  ?>" />
						</td>
						<td style="text-align:center;">
							<input type="checkbox" value="1" name="<?php echo esc_attr( $field_key ) . '_' . $idx . '_enable';  ?>" <?php echo checked( $service['enable'], 1, FALSE ) ?>/>
						</td>
					</tr>
				<?php $idx++; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->get_description_html( $data ); ?>
	</td>
</tr>