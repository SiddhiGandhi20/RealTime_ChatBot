from flask import Flask
from flask_socketio import SocketIO, send
import time
import openai
import logging

# Enable debug logging for Flask and Flask-SocketIO
logging.basicConfig(level=logging.DEBUG)

# Set up OpenAI API key (ensure this is your valid key)
openai.api_key = 'your-openai-api-key'  # Replace with your actual OpenAI API key

# Initialize the Flask app and Flask-SocketIO
app = Flask(__name__)
socketio = SocketIO(app, cors_allowed_origins="*")  # Allow connections from any origin for testing

@app.route('/')
def index():
    return "Python WebSocket server is running."

# Handle incoming WebSocket messages
@socketio.on('message')
def handle_message(message):
    print(f"Received message: {message}")
    
    # Simulate some processing time (optional)
    time.sleep(1)
    
    # Respond with "Hello" when message is "Hi"
    if message.lower() == "hi":
        response = "Hello"
    else:
        # Otherwise, get a response from OpenAI
        try:
            response = get_chatgpt_response(message)
        except Exception as e:
            response = f"Error: {str(e)}"
    
    send(response)  # Send the response back to the client

def get_chatgpt_response(message):
    try:
        # Request a response from ChatGPT
        completion = openai.ChatCompletion.create(
            model="gpt-3.5-turbo",  # Or the model of your choice
            messages=[{
                "role": "system", "content": "You are a helpful assistant."
            }, {
                "role": "user", "content": message
            }]
        )
        # Extract and return the generated response
        return completion['choices'][0]['message']['content']
    except Exception as e:
        return f"Error processing request: {str(e)}"

if __name__ == '__main__':
    socketio.run(app, host='0.0.0.0', port=5000)
