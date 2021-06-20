# Import main Flask class and request object
from flask import Flask, request
from flask_cors import CORS
import json
import paho.mqtt.client as mqtt


# Create the Flask app
app = Flask(__name__)
CORS(app)


@app.route('/')
def root():
    return 'Please POST to /post'


@app.route('/post', methods=['GET', 'POST'])
def post():
    if request.method == 'GET':
        return 'Please POST to /post'

    request_data = request.json
    print(request_data)
    
    # Get kit's id
    kId = request_data['value'].split(',')[0]
    
    # Publish recieved data to mqtt topic based on kit's id
    client.publish(str(kId), str(request_data['value']))

    return json.dumps(request_data)


def on_connect(self, client, userdata, rc):
    print("Connected with result code "+str(rc))


if __name__ == "__main__":
    client = mqtt.Client()
    client.on_connect = on_connect
    client.connect("localhost", 1883, 60)
    client.loop_start()
    app.run(host="0.0.0.0", port=1024, debug=True)
