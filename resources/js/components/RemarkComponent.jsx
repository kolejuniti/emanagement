import React from 'react';

class RemarkComponent extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      remark: '',
      data: props.data,
    };
  }

  handleRemarkChange = (event) => {
    this.setState({ remark: event.target.value });
  };

  renderTableData() {
    return this.state.data.map((item, index) => (
      <tr key={index}>
        <td>{item.ic}</td>
        <td>{item.name}</td>
        {/* Add more fields as needed */}
      </tr>
    ));
  }

  render() {
    return (
      <div>
        <div className="form-group">
          <label htmlFor="remark">Remark:</label>
          <textarea class="form-control col-md-12 mt-3" rows="10" cols="80" name="remark" id="remark" value={this.state.remark} onChange={this.handleRemarkChange}></textarea>
          {/* <input type="text" id="remark" className="form-control" value={this.state.remark} onChange={this.handleRemarkChange}
          /> */}
        </div>
        <table className="table table-bordered">
          <thead>
            <tr>
              <th>Field 1</th>
              <th>Field 2</th>
              {/* Add more headers as needed */}
            </tr>
          </thead>
          <tbody>
            {this.renderTableData()}
          </tbody>
        </table>
      </div>
    );
  }
}

export default RemarkComponent;
