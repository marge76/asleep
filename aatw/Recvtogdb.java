import com.rabbitmq.client.ConnectionFactory;
import com.rabbitmq.client.Connection;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.QueueingConsumer;
import java.sql.*;

public class Recvtogdb {

	private final static String QUEUE_NAME = "aatw_gaian";
	public static void main(String[] argv) throws Exception {
	ConnectionFactory factory = new ConnectionFactory();
	factory.setHost("localhost");
	Connection connection = factory.newConnection();
	Channel channel = connection.createChannel();
	channel.queueDeclare(QUEUE_NAME, false, false, false, null);
	System.out.println(" [*] Waiting for messages. To exit press CTRL+C");
	QueueingConsumer consumer = new QueueingConsumer(channel);
	channel.basicConsume(QUEUE_NAME, true, consumer);
	String URL = "jdbc:derby://localhost:6414/gaiandb";
	String USER = "gaiandb";
	String PASS = "passw0rd";
	java.sql.Connection conn = DriverManager.getConnection(URL, USER, PASS);
	Statement stmt = null;
	stmt = conn.createStatement();
	/* String sql;
	sql = "SELECT * FROM members";
	ResultSet rs = stmt.executeQuery(sql);
	while (rs.next()) {
		int misc  = rs.getInt("MISC");
         	String locat = rs.getString("LOCATION");
         	int numb = rs.getInt("NUMBER");
         	//Display values
         	System.out.print("LOCATION: " + locat);
         	System.out.print(", MISC: " + misc);
         	System.out.println(", NUMBER: " + numb);
	} */
	while (true) {
		QueueingConsumer.Delivery delivery = consumer.nextDelivery();
		String message = new String(delivery.getBody());
		System.out.println(" [x] Received '" + message + "'");
		boolean rs = stmt.execute(message);
		}
	}
}
