<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
<div class="box mb" id="mae-form">
	<div class="tbl-ctrls">
		<nav >
			<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td>
							<h1>Mail After Edit</h1>
						</td>
						<td style="text-align: right;">
							<div v-if="!isLoaded">
								<a v-on:click="saveSettings" class="btn">Save Settings</a>
							</div>
							<div v-else>
								<p>Saving...</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</nav>
		<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th colspan="2">
						<h2>Message Settings</h2>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							Start
						</label>
					</td>
					<td>
						<input type="text" v-model="message_config.start" />
					</td>
				</tr>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							End
						</label>
					</td>
					<td>
						<input type="text" v-model="message_config.end" />
					</td>
				</tr>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							Domain
						</label>
					</td>
					<td>
						<input type="text" v-model="message_config.domain" />
					</td>
				</tr>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							From Email
						</label>
					</td>
					<td>
						<input type="text" v-model="message_config.from" />
					</td>
				</tr>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							Force BCC
							<br />
							<small>Select this option to send the email to all intended recipients BCCed. The email `to` address will be the `from` address.</small>
						</label>
					</td>
					<td>
						<input type="checkbox" v-model="message_config.force_bcc" />
					</td>
				</tr>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							Send Individually
							<br />
							<small>Select this option to send each email individually, instead of grouping them all together.</small>
						</label>
					</td>
					<td>
						<input type="checkbox" v-model="message_config.send_individually" />
					</td>
				</tr>
			</tbody>
		</table>
		<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th colspan="2">
						<h2>Channel Information</h2>
					</th>
					<th colspan="5" style="text-align: right;">
						<a v-on:click="addChannel" class="btn">Add Channel</a>
					</th>
				</tr>
				<tr>
					<th>
						Channel
					</th>
					<th>
						Type
					</th>
					<th>
						Send To
					</th>
					<th>
						Notify Author
					</th>
					<th>
						From Email
					</th>
					<th>
						Skip Fields
					</th>
					<th>
						Actions
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(channel, idx) in channel_config" :key="channel.channel + '-' + idx">
					<td>
						<select v-model="channel.channel" v-on:change="changeChannel($event, channel.channel, 'channel')">
							<option></option>
							<option v-for="c in channels" :value="c.id">{{ c.name }}</option>
						</select>
					</td>
					<td>
						<select v-model="channel.type" v-on:change="changeChannel($event, channel.channel, 'type')">
							<option value="email">Email</option>
							<option value="member_group">{{ memberPlaceholder ? 'Member Group' : 'Role' }}</option>
						</select>
					</td>
					<td v-if="channel.type == 'email'">
						<input type="email" v-model="channel.data" v-on:change="changeChannel($event, channel.channel, 'email')"/>
					</td>
					<td v-if="channel.type == 'member_group'">
						<select v-model="channel.data" multiple v-on:change="changeChannel($event, channel.channel, 'member_group')">
							<option></option>
							<option v-for="m in memberGroups" :value="m.id">{{ m.name }}</option>
						</select>
					</td>
					<td>
						<input type="checkbox" v-model="channel.author" v-on:change="changeChannel($event, channel.channel, 'author')" />
					</td>
					<td>
						<input type="email" v-model="channel.from" v-on:change="changeChannel($event, channel.channel, 'from')" />
					</td>
					<td>
						<input type="text" v-model="channel.skip_fields" v-on:change="changeChannel($event, channel.channel, 'skip_fields')" />
					</td>
					<td>
						<ul class="toolbar">
							<li class="remove">
								<a v-on:click.prevent="removeRow(idx)" class="m-link"></a>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th colspan="2">
						<h2>Other Settings</h2>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
							Skip Fields
							<br />
							<small>Use pipes (|) to separate field names (i.e. entry_date|submit)</small>
						</label>
					</td>
					<td>
						<input type="text" v-model="skip_fields" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script>
	const app = new Vue({
		el: '#mae-form',
		data:function () {
			return {
				isLoaded: false,
				skip_fields: "<?php echo implode('|',$skip_fields); ?>",
				message_config: <?php echo json_encode($message_config); ?>,
				channel_config: <?php echo json_encode($channel_config); ?>,
				channels: <?php echo json_encode($channels); ?>,
				memberGroups: <?php echo json_encode($memberGroups); ?>,
				memberPlaceholder: "<?php echo $memberPlaceholder; ?>",
				saveUrl: "<?php echo $save_url; ?>"
			}
		},
		methods: {
			changeChannel(event, channel, type) {

				var val = event.target.value,
					out = []

				this.channel_config.forEach(function(c) {

					var deleteC = false;

					if(c.channel == channel) {
						switch (type) {
							case 'channel':
								c.channel = val
								break;
							case 'type':
								c.type = val
								break;
							case 'skip_fields':
								c.skip_fields = val
								break;
							case 'author':
								c.author = event.target.checked;
								break;
							case 'email':
								c.data = val
								break;
							case 'from':
								c.from = val
								break;
							case 'delete':
								deleteC = true;
								break;
						}
					}
					if(!deleteC) {
						out.push(c);
					}
				})

				this.channel_config = out

			},
			removeRow(idx) {
				var out = [];

				this.channel_config.forEach(function(c, index) {

					if(index !== idx) {
						out.push(c);
					}

				})

				this.channel_config = out
			},
			saveSettings: function () {

				console.log('this.channel_config', this.channel_config);

				var sendData = {
					message_config: this.message_config,
					channel_config: this.channel_config,
					skip_fields: this.skip_fields,
					csrf_token: EE.CSRF_TOKEN
				}

				this.isLoaded = false;

				$.ajax({
					url: this.saveUrl,
					method: "POST",
					data: sendData,
					datatype: "json"
				}).done(function() {
					alert('Saved');
				});

			},
			addChannel: function() {

				var ch = {
					channel: null,
					type: 'email',
					from: '',
					data: '',
				}

				this.channel_config.push(ch)

			}
		}
	});
</script>